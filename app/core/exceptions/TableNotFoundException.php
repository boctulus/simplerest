<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class TableNotFoundException extends BaseException
{
    protected $errorCode = 'DB>TABLE_NOT_FOUND';
}
