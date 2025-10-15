<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class SchemaException extends BaseException
{
    protected $errorCode = 'DB>SCHEMA_ERROR';
}