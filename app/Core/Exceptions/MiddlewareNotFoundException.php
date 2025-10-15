<?php

namespace Boctulus\Simplerest\Core\Exceptions;

use Boctulus\Simplerest\Core\Exceptions\BaseException;

class MiddlewareNotFoundException extends BaseException
{
    protected static string $errorCode = 'HTTP>MIDDLEWARE_NOT_FOUND';
}