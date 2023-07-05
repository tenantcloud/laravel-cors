<?php

namespace TenantCloud\Cors\Exceptions;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

/**
 * CORS version of Forbidden HTTP exception.
 */
class CorsForbiddenException extends HttpException
{
	public function __construct(Throwable $previous = null, array $headers = [], ?int $code = 0)
	{
		parent::__construct(Response::HTTP_FORBIDDEN, 'Forbidden (cors).', $previous, $headers, $code);
	}
}
