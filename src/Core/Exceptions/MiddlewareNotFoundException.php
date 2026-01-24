<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class MiddlewareNotFoundException extends BaseException
{
    protected static string $errorCode = 'HTTP>MIDDLEWARE_NOT_FOUND';
}