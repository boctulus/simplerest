<?php

use Boctulus\Simplerest\Core\Libs\CronJob;
use Boctulus\Simplerest\Core\Libs\Logger;

class SomeJob extends CronJob
{
	static protected $month;
    static protected $monthday;
	static protected $weekday;
	static protected $hour; 
	static protected $minute;
	static protected $second = 2;
	static protected $is_active = true;

	/*
		Number of retries in 24 Hs.
	*/
	static protected $retries = 3;
    static protected $retry_timeframe = 10;


	function run(){
		// your logic here
		echo "hi ";
		Logger::log(get_class());

		$num = rand(1,10);

		// if ($num >5){
		// 	throw new \Exception("Some exception");
		// }

		//sleep(5);

		//exit(2);
	}

	function onFail(\Exception $error, int $times){
		Logger::log(get_class() . " has failed $times times. Now with ". $error->getMessage());
	}

}
