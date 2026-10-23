<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Exceptions;

class MiddlewareNotFoundException extends BaseException
{
    protected int $httpStatus = 503;
}
