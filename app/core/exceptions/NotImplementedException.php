<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class NotImplementedException extends BaseException
{
    protected static string $errorCode = 'GENERAL>NOT_IMPLEMENTED';
}
