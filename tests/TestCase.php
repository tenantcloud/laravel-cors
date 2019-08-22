<?php

namespace TenantCloud\Cors\Tests;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;
use TenantCloud\Cors\CorsMiddleware;
use TenantCloud\Cors\CorsServiceProvider;

class TestCase extends OrchestraTestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$this->setupRoutes();
	}

	/**
	 * Set up routes for the tests.
	 */
	protected function setupRoutes(): void
	{
		Route::post('test-cors', static function () {
			return 'real content';
		});

		Route::post('test-cors-stream', static function () {
			return new StreamedResponse();
		});
	}

	/**
	 * @param Application $app
	 */
	protected function getEnvironmentSetUp($app): void
	{
		$app['config']->set('app.debug', true);

		$app->make(Kernel::class)->prependMiddleware(CorsMiddleware::class);
	}

	/**
	 * @param Application $app
	 *
	 * @return array
	 */
	protected function getPackageProviders($app): array
	{
		return [
			CorsServiceProvider::class,
		];
	}
}
