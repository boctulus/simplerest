<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class InvalidValidationException extends BaseException
{
    protected $errorCode = 'VALIDATION>INVALID_VALIDATION';
}
