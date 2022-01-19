<?php

use simplerest\core\libs\BackgroundService;
use simplerest\core\libs\Files;

class OtherWorker extends BackgroundService 
{
	static protected $month;
    static protected $monthday;
	static protected $weekday = 3;
	static protected $hour    = 8;
	static protected $minute  = 2;
	static protected $second;

	function start(){
		// your logic here
		Files::logger(get_class());
	}

	function stop(){
		// your logic here
	}
}
