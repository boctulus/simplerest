<?php

namespace Boctulus\Simplerest\Core\Exceptions;

/**
 * The request body could not be decoded for its declared Content-Type.
 */
class InvalidRequestBodyException extends BaseException
{
    protected static string $errorCode = 'INVALID_REQUEST_BODY';

    protected int $httpStatus = 400;
}
