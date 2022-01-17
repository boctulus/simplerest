<?php

use simplerest\core\libs\BackgroundService;
use simplerest\core\libs\Files;

class SomeWorker extends BackgroundService 
{
	static protected $month;
    static protected $monthday;
	static protected $weekday;
	static protected $hour;
	static protected $minute;
	static protected $second = 3;


	function start(){
		// your logic here
		Files::logger(rand(50,99));
	}

	function stop(){
		// your logic here
	}
}
