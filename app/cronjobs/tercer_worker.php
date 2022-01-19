<?php

use simplerest\core\libs\BackgroundService;
use simplerest\core\libs\Files;

class TercerWorker extends BackgroundService 
{
	static protected $month;
    static protected $monthday;
	static protected $weekday;
	static protected $hour   = 21;
	static protected $minute = 5;
	static protected $second;


	function start(){
		// your logic here
		Files::logger(get_class());
	}

	function stop(){
		// your logic here
	}
}
