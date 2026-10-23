<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Exceptions;

class InvalidRequestBodyException extends BaseException
{
    protected int $httpStatus = 400;
}
