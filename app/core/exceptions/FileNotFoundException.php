<?php

namespace Boctulus\Simplerest\Core\Exceptions;

class FileNotFoundException extends BaseException
{
    protected $errorCode = 'FILES>FILE_NOT_FOUND';
}
