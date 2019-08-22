<?php

namespace TenantCloud\Cors\Tests;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;

class CorsTest extends TestCase
{
	public function testItAddsTheCorsHeadersOnAValidRequests(): void
	{
		$this
			->sendRequest('POST', 'https://tenantcloud.com')
			->assertSuccessful()
			->assertHeader('Access-Control-Allow-Origin', '*')
			->assertSee('real content');
	}

	public function testItAddsTheWildcardInTheCorsHeadersOnAValidRequestIfNoAllowOriginsAreSet(): void
	{
		$this
			->sendRequest('POST', 'https://tenantcloud.com')
			->assertSuccessful()
			->assertHeader('Access-Control-Allow-Origin', '*')
			->assertSee('real content');
	}

	public function testItAddsTheCredentialsCorsHeadersOnAValidRequestIfAllowCredentialsIsSetToTrue(): void
	{
		config()->set('cors.profiles.default.allow_credentials', true);

		$this
			->sendRequest('POST', 'https://tenantcloud.com')
			->assertSuccessful()
			->assertHeader('Access-Control-Allow-Credentials', 'true')
			->assertHeader('Access-Control-Allow-Origin', 'https://tenantcloud.com')
			->assertSee('real content');
	}

	public function testItDoesNotAddTheCredentialsCorsHeadersOnAValidRequestIfAllowCredentialsIsSetToFalse(): void
	{
		config()->set('cors.profiles.default.allow_credentials', false);

		$response = $this
			->sendRequest('POST', 'https://tenantcloud.com')
			->assertSuccessful()
			->assertHeader('Access-Control-Allow-Origin', '*')
			->assertSee('real content');

		$headerName = 'Access-Control-Allow-Credentials';

		$this->assertFalse($response->headers->has($headerName), "Unexpected header [{$headerName}] is present on response.");
	}

	public function testItAddsTheOriginDomainInTheCorsHeadersOnAValidRequest(): void
	{
		config()->set('cors.profiles.default.allow_origins', [
			'https://tenantcloud.com',
			'https://laravel.com',
		]);

		$this
			->sendRequest('POST', 'https://tenantcloud.com')
			->assertSuccessful()
			->assertHeader('Access-Control-Allow-Origin', 'https://tenantcloud.com')
			->assertSee('real content');
	}

	public function testItAddsTheOriginDomainInTheCorsHeadersOnAValidRequestWithWildcardContent(): void
	{
		config()->set('cors.profiles.default.allow_origins', [
			'https://*.be',
			'https://*.com',
		]);

		$this
			->sendRequest('POST', 'https://tenantcloud.com')
			->assertSuccessful()
			->assertHeader('Access-Control-Allow-Origin', 'https://tenantcloud.com')
			->assertSee('real content');
	}

	public function testItThrowsErrorOnAnInvalidRequestWithWildcardContent(): void
	{
		config()->set('cors.profiles.default.allow_origins', [
			'https://*.tenantcloud.com',
			'https://*.suka',
		]);

		$this
			->sendRequest('POST', 'https://tenantcloud.com')
			->assertStatus(Response::HTTP_FORBIDDEN)
			->assertSee('Forbidden (cors).');
	}

	public function testItAddsTheAllowedExposeHeadersInTheCorsHeadersOnAValidRequest(): void
	{
		config()->set('cors.profiles.default.expose_headers', [
			'Authorization',
			'X-Foo-Header',
		]);

		$this
			->sendRequest('POST', 'https://tenantcloud.com')
			->assertSuccessful()
			->assertHeader('Access-Control-Expose-Headers', 'Authorization, X-Foo-Header')
			->assertHeader('Access-Control-Allow-Origin', '*')
			->assertSee('real content');
	}

	public function testItWillSendA403ForInvalidRequests(): void
	{
		config()->set('cors.profiles.default.allow_origins', ['https://tenantcloud.com']);

		$this
			->sendRequest('POST', 'https://laravel.com')
			->assertStatus(Response::HTTP_FORBIDDEN)
			->assertSee('Forbidden (cors).');
	}

	public function testItWillBeAValidProfileIfExposeHeaderIsNotSet(): void
	{
		config()->set('cors.profiles.default.expose_headers', null);

		$this
			->sendRequest('POST', 'https://tenantcloud.com')
			->assertSuccessful()
			->assertHeader('Access-Control-Expose-Headers', '')
			->assertHeader('Access-Control-Allow-Origin', '*')
			->assertSee('real content');
	}

	public function testItAddsTheCorsHeadersOnAValidStreamRequest(): void
	{
		$this
			->sendRequest('POST', 'https://tenantcloud.com', 'test-cors-stream')
			->assertSuccessful()
			->assertHeader('Access-Control-Allow-Origin', '*');
	}

	public function sendRequest(string $method, string $origin, string $uri = 'test-cors'): TestResponse
	{
		$headers = [
			'Origin' => $origin,
		];

		$server = $this->transformHeadersToServerVars($headers);

		return $this->call($method, $uri, [], [], [], $server);
	}
}
