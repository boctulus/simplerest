<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class ColumnTableNotFoundException extends BaseException
{
    protected $errorCode = 'DB>COLUMN_NOT_FOUND';
}
