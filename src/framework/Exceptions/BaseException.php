<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Exceptions;

use RuntimeException;

abstract class BaseException extends RuntimeException
{
    protected int $httpStatus = 500;

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }
}
