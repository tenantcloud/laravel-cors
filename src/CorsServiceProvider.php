<?php

namespace TenantCloud\Cors;

use Illuminate\Support\ServiceProvider;

class CorsServiceProvider extends ServiceProvider
{
	/**
	 * {@inheritDoc}
	 */
	public function boot(): void
	{
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/../config/cors.php' => config_path('cors.php'),
			], 'config');
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function register(): void
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/cors.php', 'cors');
	}
}
