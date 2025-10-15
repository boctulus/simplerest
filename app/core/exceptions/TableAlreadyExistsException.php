<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class TableAlreadyExistsException extends BaseException
{
    protected $errorCode = 'DB>TABLE_ALREADY_EXISTS';
}
