<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class SqlException extends BaseException
{
    protected static string $errorCode = 'DB>SQL_EXCEPTION';
}