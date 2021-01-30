<?php

namespace TenantCloud\Cors\Tests;

use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;

class PreflightTest extends TestCase
{
	public function testItRespondsWithA204ForAValidPreflightRequest(): void
	{
		$response = $this
			->sendPreflightRequest('DELETE', 'https://tenantcloud.com')
			->assertStatus(Response::HTTP_NO_CONTENT)
			->assertHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, PATCH, DELETE')
			->assertHeader('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Authorization')
			->assertHeader('Access-Control-Allow-Origin', '*')
			->assertHeader('Access-Control-Max-Age', 60 * 60 * 24);

		$this->assertEmpty($response->content());
	}

	public function testItRespondsWithA403ForAPreflightRequestWithAnInvalidMethod(): void
	{
		config()->set('cors.profiles.default.allow_methods', ['GET']);

		$this
			->sendPreflightRequest('DELETE', 'https://tenantcloud.com')
			->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function testItRespondsWithCorrectHeaderForAPreflightRequestWhenAllowCredentialsIsSetToTrue(): void
	{
		config()->set('cors.profiles.default.allow_credentials', true);

		$this
			->sendPreflightRequest('DELETE', 'https://tenantcloud.com')
			->assertHeader('Access-Control-Allow-Credentials', 'true')
			->assertHeader('Access-Control-Allow-Origin', 'https://tenantcloud.com');
	}

	public function testItRespondsWithCorrectHeaderForAPreflightRequestWhenAllowCredentialsIsSetToFalse(): void
	{
		config()->set('cors.profiles.default.allow_credentials', false);

		$response = $this
			->sendPreflightRequest('DELETE', 'https://tenantcloud.com')
			->assertHeader('Access-Control-Allow-Origin', '*');

		$headerName = 'Access-Control-Allow-Credentials';

		$this->assertFalse($response->headers->has($headerName), "Unexpected header [{$headerName}] is present on response.");
	}

	public function testItRespondsWithA204ForAPreflightRequestComingFromAnAllowedOrigin(): void
	{
		config()->set('cors.profiles.default.allow_origins', ['https://tenantcloud.com']);

		$this
			->sendPreflightRequest('DELETE', 'https://tenantcloud.com')
			->assertStatus(Response::HTTP_NO_CONTENT);
	}

	public function testItRespondsWithA403ForAPreflightRequestWithAnInvalidOrigin(): void
	{
		config()->set('cors.profiles.default.allow_origins', ['https://tenantcloud.com']);

		$this
			->sendPreflightRequest('DELETE', 'https://laravel.com')
			->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function sendPreflightRequest(string $method, string $origin): TestResponse
	{
		$headers = [
			'Access-Control-Request-Method' => $method,
			'Origin'                        => $origin,
		];

		$server = $this->transformHeadersToServerVars($headers);

		return $this->call('OPTIONS', 'test-cors', [], [], [], $server);
	}
}
