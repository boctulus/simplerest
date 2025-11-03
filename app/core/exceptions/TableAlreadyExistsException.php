<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class TableAlreadyExistsException extends BaseException
{
    protected static string $errorCode = 'DB>TABLE_ALREADY_EXISTS';
}
