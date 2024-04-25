# Laravel CORS

Laravel package for CORS, with support for custom CORS profiles per route.

## Installation

In your `composer.json`, add this repository:

```
"repositories": [
    {
        "type": "git",
        "url": "https://github.com/tenantcloud/laravel-cors"
    }
],
```

Then do `composer require tenantcloud/laravel-cors` to install the package.
After, publish the config: `php artisan vendor:publish` and select needed config file.

## Usage

### You want to use a single, global CORS per project

If you want to use a global profile, add `CorsMiddleware::class` into your `Http\Kernel.php`'s
`$middleware`. `default` profile from the config will be used.

### You want to use multiple different CORS profiles per route

If you need a scoped CORS, you should:

1. Add wanted config to cors.php
2. Add `'cors' => CorsMiddleware::class` into your `Http\Kernel.php`'s `$routeMiddleware`.
3. Add `cors:your_profile` to route you want to have CORS on.
4. Add `OPTIONS` to list of supported methods for that route.

Example:

```
Route::match(['GET', 'OPTIONS'], '/test', 'Controller@test')->middleware('cors:test_profile');
```
