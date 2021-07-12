<?php

namespace TenantCloud\Cors\Profile;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractCorsProfile
{
	/** @var Request */
	protected $request;

	/**
	 * Value of 'Access-Control-Allow-Credentials' header.
	 */
	abstract public function allowCredentials(): bool;

	/**
	 * Value of 'Access-Control-Allow-Origin' header.
	 */
	abstract public function allowOrigins(): array;

	/**
	 * Value of 'Access-Control-Allow-Methods' header.
	 */
	abstract public function allowMethods(): array;

	/**
	 * Value of 'Access-Control-Allow-Headers' header.
	 */
	abstract public function allowHeaders(): array;

	/**
	 * Value of 'Access-Control-Expose-Headers' header.
	 */
	abstract public function exposeHeaders(): array;

	/**
	 * Value of 'Access-Control-Max-Age' header.
	 */
	abstract public function maxAge(): int;

	/**
	 * @return static
	 */
	public function setRequest(Request $request): self
	{
		$this->request = $request;

		return $this;
	}

	/**
	 * Whether current request is allowed by CORS.
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
	 */
	public function addCorsHeaders(Response $response): void
	{
		$this->addCommonHeaders($response);

		$response->headers->set('Access-Control-Expose-Headers', $this->headerItemsToString($this->exposeHeaders()));
	}

	/**
	 * Adds CORS headers to preflight request response.
	 */
	public function addPreflightHeaders(Response $response): void
	{
		$this->addCommonHeaders($response);

		$response->headers->set('Access-Control-Allow-Methods', $this->headerItemsToString($this->allowMethods()));
		$response->headers->set('Access-Control-Allow-Headers', $this->headerItemsToString($this->allowHeaders()));
		$response->headers->set('Access-Control-Max-Age', $this->maxAge());
	}

	/**
	 * Adds any common headers between the two.
	 */
	protected function addCommonHeaders(Response $response): void
	{
		if ($this->allowCredentials()) {
			// Yes, should be string, because real boolean gets converted to 1.
			$response->headers->set('Access-Control-Allow-Credentials', 'true');
		}

		$response->headers->set('Access-Control-Allow-Origin', $this->allowedOriginsAsString());
	}

	/**
	 * Returns all allowed origins for this request as a string (for the header).
	 */
	protected function allowedOriginsAsString(): string
	{
		if (!$this->isAllowed()) {
			return '';
		}

		if (in_array('*', $this->allowOrigins(), true) && !$this->allowCredentials()) {
			return '*';
		}

		return $this->request->header('Origin');
	}

	/**
	 * Transforms an array of header enumerated values as a string.
	 */
	protected function headerItemsToString(array $array): string
	{
		return implode(', ', $array);
	}
}
