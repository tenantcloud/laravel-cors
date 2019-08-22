<?php

namespace TenantCloud\Cors\Profile;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractCorsProfile
{
	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * Value of 'Access-Control-Allow-Credentials' header.
	 *
	 * @return bool
	 */
	abstract public function allowCredentials(): bool;

	/**
	 * Value of 'Access-Control-Allow-Origin' header.
	 *
	 * @return array
	 */
	abstract public function allowOrigins(): array;

	/**
	 * Value of 'Access-Control-Allow-Methods' header.
	 *
	 * @return array
	 */
	abstract public function allowMethods(): array;

	/**
	 * Value of 'Access-Control-Allow-Headers' header.
	 *
	 * @return array
	 */
	abstract public function allowHeaders(): array;

	/**
	 * Value of 'Access-Control-Expose-Headers' header.
	 *
	 * @return array
	 */
	abstract public function exposeHeaders(): array;

	/**
	 * Value of 'Access-Control-Max-Age' header.
	 *
	 * @return int
	 */
	abstract public function maxAge(): int;

	/**
	 * @param Request $request
	 *
	 * @return static
	 */
	public function setRequest(Request $request): self
	{
		$this->request = $request;

		return $this;
	}

	/**
	 * Whether current request is allowed by CORS.
	 *
	 * @return bool
	 */
	public function isAllowed(): bool
	{
		if (!in_array($this->request->method(), $this->allowMethods(), true)) {
			return false;
		}

		if (in_array('*', $this->allowOrigins(), true)) {
			return true;
		}

		return collect($this->allowOrigins())->contains(function ($allowedOrigin) {
			return fnmatch($allowedOrigin, $this->request->header('Origin'));
		});
	}

	/**
	 * Adds CORS headers to regular request response.
	 *
	 * @param Response $response
	 */
	public function addCorsHeaders(Response $response): void
	{
		$this->addCommonHeaders($response);

		$response->headers->set('Access-Control-Expose-Headers', $this->toString($this->exposeHeaders()));
	}

	/**
	 * Adds CORS headers to preflight request response.
	 *
	 * @param Response $response
	 */
	public function addPreflightHeaders(Response $response): void
	{
		$this->addCommonHeaders($response);

		$response->headers->set('Access-Control-Allow-Methods', $this->toString($this->allowMethods()));
		$response->headers->set('Access-Control-Allow-Headers', $this->toString($this->allowHeaders()));
		$response->headers->set('Access-Control-Max-Age', $this->maxAge());
	}

	/**
	 * Adds any common headers between the two.
	 *
	 * @param Response $response
	 */
	protected function addCommonHeaders(Response $response): void
	{
		if ($this->allowCredentials()) {
			// Yes, should be string, because real boolean gets converted to 1.
			$response->headers->set('Access-Control-Allow-Credentials', 'true');
		}

		$response->headers->set('Access-Control-Allow-Origin', $this->allowedOriginsToString());
	}

	protected function allowedOriginsToString(): string
	{
		if (!$this->isAllowed()) {
			return '';
		}

		if (in_array('*', $this->allowOrigins(), true) && !$this->allowCredentials()) {
			return '*';
		}

		return $this->request->header('Origin');
	}

	protected function toString(array $array): string
	{
		return implode(', ', $array);
	}
}
