<?php

namespace simplerest\core;

use InvalidArgumentException;
use PHPUnit\Util\InvalidArgumentHelper;

class Container 
{
    static protected $bindings = [];

    static public function bind(string $key, $value)
    {
        static::$bindings[$key] = $value;
    }

    static public function make(string $key)
    {
        if (!isset(static::$bindings[$key])) {
            throw new InvalidArgumentException("Class not found");          
        }

        return (static::$bindings[$key])->__invoke();
    }

}