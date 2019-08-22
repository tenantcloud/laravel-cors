<?php

namespace TenantCloud\Cors\Tests\Stubs;

use TenantCloud\Cors\Profile\AbstractCorsProfile;

class TestCorsProfileStub extends AbstractCorsProfile
{
	/**
	 * Value of 'Access-Control-Allow-Credentials' header.
	 *
	 * @return bool
	 */
	public function allowCredentials(): bool
	{
		return true;
	}

	/**
	 * Value of 'Access-Control-Allow-Origin' header.
	 *
	 * @return array
	 */
	public function allowOrigins(): array
	{
		return ['https://nesto.com'];
	}

	/**
	 * Value of 'Access-Control-Allow-Methods' header.
	 *
	 * @return array
	 */
	public function allowMethods(): array
	{
		return ['GET', 'POST'];
	}

	/**
	 * Value of 'Access-Control-Allow-Headers' header.
	 *
	 * @return array
	 */
	public function allowHeaders(): array
	{
		return [];
	}

	/**
	 * Value of 'Access-Control-Expose-Headers' header.
	 *
	 * @return array
	 */
	public function exposeHeaders(): array
	{
		return [];
	}

	/**
	 * Value of 'Access-Control-Max-Age' header.
	 *
	 * @return int
	 */
	public function maxAge(): int
	{
		return 0;
	}
}
