<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class NotFileButDirectoryException extends BaseException
{
    protected static string $errorCode = 'FILES>NOT_A_FILE';
}
