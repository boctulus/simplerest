<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class NotFileButDirectoryException extends BaseException
{
    protected $errorCode = 'FILES>NOT_A_FILE';
}
