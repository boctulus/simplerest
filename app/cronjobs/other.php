<?php

use simplerest\core\libs\BackgroundService;
use simplerest\core\libs\Files;

class OtherJob extends BackgroundService 
{
	static protected $month;
    static protected $monthday;
	static protected $weekday = 3;
	static protected $hour    = 20;
	static protected $minute  = 10;
	static protected $second;

	function run(){
		// your logic here
		Files::logger(get_class());
	}
}
