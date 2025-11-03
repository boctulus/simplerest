<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class InvalidValidationException extends BaseException
{
    protected static string $errorCode = 'VALIDATION>INVALID_VALIDATION';
}
