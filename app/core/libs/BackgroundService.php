<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\Model;
use simplerest\core\libs\DB;

class BackgroundService
{
    static protected $month;
    static protected $monthday;
	static protected $weekday;
	static protected $hour;
	static protected $minute;
    static protected $second;
    static protected $is_active = true;

    const SUN = 0;
    const MON = 1;
    const TUE = 2;
    const WED = 3;
    const THU = 4;
    const FRI = 5;
    const SAT = 6;

    protected int $fails = 0;

    function __construct() {  
    }

    static function getFrequency(){
        return [
            'month' => static::$month,
            'monthday' => static::$monthday,  
            'weekday' => static::$weekday,
            'hour' => static::$hour,
            'minute' => static::$minute,
            'second' => static::$second
        ];
    }

    function start(){
        try {
            $this->run();
        } catch (\Exception $e){
            $this->fails++;
            $this->onError($e, $this->fails);
        }
    }

	function run(){
    }

    /*
        @paran $error Exception object
        @param $times int number of fails
    */
    function onError(\Exception $error, int $times){
    }
}

