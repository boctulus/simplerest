<?php

use simplerest\core\libs\CronJob;
use simplerest\core\libs\Files;

class OtherJob extends CronJob
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