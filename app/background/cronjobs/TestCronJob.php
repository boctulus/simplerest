<?php

use simplerest\core\libs\Logger;
use simplerest\core\libs\CronJob;

class TestCronJob extends CronJob
{
	static protected $month;
    static protected $monthday;
	static protected $weekday;
	static protected $hour = 21; 
	static protected $minute;
	static protected $second = 2;
	static protected $is_active = true;

	/*
		Number of retries in 24 Hs.
	*/
	static protected $retries = 3;
    static protected $retry_timeframe = 3600 * 24;


	function run(){
		// Codigo a ejecutar periodicamente. Ej:
		Logger::log(__CLASS__);
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
