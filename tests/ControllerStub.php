<?php

namespace TenantCloud\Cors\Tests;

use Illuminate\Routing\Controller;

class ControllerStub extends Controller
{
	public function __construct()
	{
		$this->middleware(fn ($request, $next) => $next($request));
	}

	public function endpoint(): void {}
}
