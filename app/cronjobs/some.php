<?php

use simplerest\core\libs\BackgroundService;
use simplerest\core\libs\Files;

class SomeJob extends BackgroundService 
{
	static protected $month;
    static protected $monthday;
	static protected $weekday;
	static protected $hour; 
	static protected $minute;
	static protected $second = 1;
	static protected $is_active = true;
	static protected $dontOverlap = true;

	/*
		Number of retries in 24 Hs.
	*/
	static protected $retries = 3;
    static protected $retry_timeframe = 10;


	function run(){
		// your logic here
		echo "hi ";
		Files::logger(get_class());

		$num = rand(1,10);

		if ($num >5){
			throw new \Exception("Some exception");
		}

		//exit(2);
	}

	function onFail(\Exception $error, int $times){
		Files::logger(get_class() . " has failed $times times. Now with ". $error->getMessage());
	}

}
