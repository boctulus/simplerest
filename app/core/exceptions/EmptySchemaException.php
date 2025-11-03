<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class EmptySchemaException extends BaseException
{
    protected static string $errorCode = 'DB>EMPTY_SCHEMA';
}
