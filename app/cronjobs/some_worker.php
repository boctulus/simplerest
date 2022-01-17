<?php

use simplerest\core\libs\BackgroundService;

class SomeWorker extends BackgroundService 
{
	static protected $month;
    static protected $monthday;
	static protected $weekday;
	static protected $hour;
	static protected $minute;
	static protected $second = 2;


	function start(){
		// your logic here
		d(rand(50,99));
	}

	function stop(){
		// your logic here
	}
}
