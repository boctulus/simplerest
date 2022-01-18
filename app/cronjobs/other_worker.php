<?php

use simplerest\core\libs\BackgroundService;
use simplerest\core\libs\Files;

class OtherWorker extends BackgroundService 
{
	static protected $month;
    static protected $monthday;
	static protected $weekday;
	static protected $hour   = 11;
	static protected $minute;
	static protected $second = 1;

	function start(){
		// your logic here
		Files::logger('Starting ' . get_class());
	}

	function stop(){
		// your logic here
	}
}
