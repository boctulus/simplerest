<?php

use simplerest\core\libs\BackgroundService;

class OtherWorker extends BackgroundService 
{
	static protected $month;
    static protected $monthday;
	static protected $weekday;
	static protected $hour;
	static protected $minute;
	static protected $second = 5;

	function start(){
		// your logic here
		d('Starting ' . get_class());
	}

	function stop(){
		// your logic here
	}
}
