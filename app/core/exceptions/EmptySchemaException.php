<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class EmptySchemaException extends BaseException
{
    protected $errorCode = 'DB>EMPTY_SCHEMA';
}
