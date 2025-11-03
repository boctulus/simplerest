<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class SchemaException extends BaseException
{
    protected static string $errorCode = 'DB>SCHEMA_ERROR';
}