<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class TableNotFoundException extends BaseException
{
    protected static string $errorCode = 'DB>TABLE_NOT_FOUND';
}
