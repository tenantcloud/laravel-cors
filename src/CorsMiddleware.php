<?php

namespace TenantCloud\Cors;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use TenantCloud\Cors\Exceptions\CorsForbiddenException;
use TenantCloud\Cors\Profile\AbstractCorsProfile;
use TenantCloud\Cors\Profile\ConfigCorsProfile;

/**
 * Handles CORS.
 */
class CorsMiddleware
{
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
		if (!$this->isCorsRequest($request)) {
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
