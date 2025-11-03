<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class FileNotFoundException extends BaseException
{
    protected static string $errorCode = 'FILES>FILE_NOT_FOUND';
}
