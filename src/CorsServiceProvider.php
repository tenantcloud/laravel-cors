<?php

namespace TenantCloud\Cors;

use Illuminate\Support\ServiceProvider;

class CorsServiceProvider extends ServiceProvider
{
	public function boot(): void
	{
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/../resources/config/cors.php' => config_path('cors.php'),
			], 'config');
		}
	}

	public function register(): void
	{
		$this->mergeConfigFrom(__DIR__ . '/../resources/config/cors.php', 'cors');
	}
}
