<?php

namespace TenantCloud\Cors;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use InvalidArgumentException;
use TenantCloud\Cors\Exceptions\CorsForbiddenException;
use TenantCloud\Cors\Profile\AbstractCorsProfile;
use TenantCloud\Cors\Profile\ConfigCorsProfile;

/**
 * Handles CORS.
 */
class CorsMiddleware
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param Closure $next
	 * @param string|null $profile
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next, ?string $profile = null)
	{
		if (!$this->isCorsRequest($request) || !$this->isLastApplied($request, $profile)) {
			return $next($request);
		}

		$profile = $this->resolveProfile($profile);
		$profile->setRequest($request);

		if (!$profile->isAllowed()) {
			return $this->forbidden();
		}

		if ($this->isPreflightRequest($request)) {
			$response = response(null, Response::HTTP_NO_CONTENT);

			$profile->addPreflightHeaders($response);

			return $response;
		}

		$response = $next($request);

		$profile->addCorsHeaders($response);

		return $response;
	}

    /**
     * Whether this is the last middleware for current route.
	 *
	 * We need to this to allow to "overwrite" cors settings. Unfortunately, Laravel does not provide any sort of
	 * middleware exclusion mechanism nor does it provide a proper way of doing this, so we're relying on hacky stuff here.
     *
     * @param Request $request
     * @param string|null $profile
     *
     * @return bool
     */
	private function isLastApplied($request, ?string $profile = null): bool
    {
        $route = $this->router
            ->getRoutes()
            ->match($request);

        // If no route was matched, there can't be any more middleware other than the global ones.
        if (!$route) {
            return true;
        }

        $middleware = $this->router
            ->gatherRouteMiddleware($route);

        foreach (array_reverse($middleware) as $middleware) {
        	$middleware = explode(':', $middleware);

            // Ignore all middleware that aren't this.
            if ($middleware[0] !== static::class) {
                continue;
            }

            return $profile === ($middleware[1] ?? null);
        }

        // If no middleware were found, this is the "last" one.
        return true;
    }

	/**
	 * Detects if CORS should be even checked for given request.
	 *
	 * @param Request $request
	 *
	 * @return bool
	 */
	protected function isCorsRequest($request): bool
	{
		if (!$request->headers->has('Origin')) {
			return false;
		}

		return $request->headers->get('Origin') !== $request->getSchemeAndHttpHost();
	}

	/**
	 * Whether given request is a preflight request (one browser sends as OPTIONS before sending actual request).
	 *
	 * @param Request $request
	 *
	 * @return bool
	 */
	protected function isPreflightRequest($request): bool
	{
		return $request->isMethod('OPTIONS');
	}

	/**
	 * Resolves an instance of AbstractCorsProfile based on passed string to the middleware.
	 *
	 * @param string|null $profile
	 *
	 * @return AbstractCorsProfile
	 */
	protected function resolveProfile(?string $profile = null): AbstractCorsProfile
	{
		if ($profile === null) {
			$profile = 'default';
		}

		if (config()->has("cors.profiles.$profile")) {
			return new ConfigCorsProfile($profile);
		}

		if (class_exists($profile) && is_subclass_of($profile, AbstractCorsProfile::class)) {
			return resolve($profile);
		}

		throw new InvalidArgumentException(
			"Profile $profile specified is neither a config profile nor is a class extending " . AbstractCorsProfile::class
		);
	}

	/**
	 * You can return a response from here instead if needed.
	 *
	 * @return mixed
	 */
	protected function forbidden()
	{
		throw new CorsForbiddenException();
	}
}
