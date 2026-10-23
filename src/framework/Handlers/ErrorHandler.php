<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Handlers;

use Boctulus\Simplerest\Core\Exceptions\BaseException;
use Boctulus\Simplerest\Core\Response;
use Throwable;

final class ErrorHandler
{
    public function handle(Throwable $error): Response
    {
        if ($error instanceof BaseException) {
            return Response::json(['error' => $error->getMessage()], $error->getHttpStatus());
        }

        error_log($error->getMessage());
        return Response::json(['error' => 'Internal Server Error'], 500);
    }
}
