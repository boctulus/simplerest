<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\Model;
use simplerest\core\libs\DB;

abstract class BackgroundService
{
    static protected $month;
    static protected $monthday;
	static protected $weekday;
	static protected $hour;
	static protected $minute;
    static protected $second;
    static protected $is_active = true;
    static protected $dontOverlap = false;

    /*
		Number of retries in 24 Hs.
	*/
	static protected $retries;
    static protected $retry_timeframe = 3600 * 24;

    const SUN = 0;
    const MON = 1;
    const TUE = 2;
    const WED = 3;
    const THU = 4;
    const FRI = 5;
    const SAT = 6;

    protected $fails = [];

    function __construct() {  
    }

    static function canOverlap() : bool {
        return static::$dontOverlap;
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

    function start()
    {
        try {
            $this->run();
            $this->onSuccess();
        } catch (\Exception $e){
            if (static::$retries !== null){
                /*
                    Number of retries are in some timeframe 
                    =>
                    oldest retries which are not in the timeframe are retired 
                */
                foreach ($this->fails as $ix => $t){
                    if ($t + static::$retry_timeframe < time() ){
                        unset($this->fails[$ix]);
                    }
                }

                if (static::$retries === count($this->fails ?? [])){
                    exit;
                }
            }

            $this->fails[] = time();
            $this->onFail($e, count($this->fails));
        }
    }

	function run(){
    }

    /*
        @paran $error Exception object
        @param $times int number of fails
    */
    function onFail(\Exception $error, int $times){
    }

    function onSuccess(){

    }
}

