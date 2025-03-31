<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Factory;

class Task
{   
    static protected $priority = 10;
    static protected $exec_time_limit   ;
    static protected $memory_limit;
    static protected $overlap = false;
    static protected $is_active = true;

    function __construct() { }

    static function canOverlap() : bool {
        return static::$overlap;
    }

    static function isActive() : bool {
        return static::$is_active;
    }

}

