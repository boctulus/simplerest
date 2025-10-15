<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class SqlException extends BaseException
{
    protected $errorCode = 'DB>SQL_EXCEPTION';
}