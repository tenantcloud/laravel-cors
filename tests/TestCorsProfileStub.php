<?php

namespace TenantCloud\Cors\Tests;

use TenantCloud\Cors\Profile\AbstractCorsProfile;

class TestCorsProfileStub extends AbstractCorsProfile
{
	/**
	 * Value of 'Access-Control-Allow-Credentials' header.
	 */
	public function allowCredentials(): bool
	{
		return true;
	}

	/**
	 * Value of 'Access-Control-Allow-Origin' header.
	 */
	public function allowOrigins(): array
	{
		return ['https://nesto.com'];
	}

	/**
	 * Value of 'Access-Control-Allow-Methods' header.
	 */
	public function allowMethods(): array
	{
		return ['GET', 'POST'];
	}

	/**
	 * Value of 'Access-Control-Allow-Headers' header.
	 */
	public function allowHeaders(): array
	{
		return [];
	}

	/**
	 * Value of 'Access-Control-Expose-Headers' header.
	 */
	public function exposeHeaders(): array
	{
		return [];
	}

	/**
	 * Value of 'Access-Control-Max-Age' header.
	 */
	public function maxAge(): int
	{
		return 0;
	}
}
