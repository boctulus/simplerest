<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class ColumnTableNotFoundException extends BaseException
{
    protected static string $errorCode = 'DB>COLUMN_NOT_FOUND';
}
