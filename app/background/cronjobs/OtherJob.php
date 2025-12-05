<?php

use Boctulus\Simplerest\Core\Libs\CronJob;
use Boctulus\Simplerest\Core\Libs\Logger;

class OtherJob extends CronJob
{
	static protected $month;
    static protected $monthday;
	static protected $weekday = 1; // lunes
	static protected $hour    = 17;
	static protected $minute  = 25;
	static protected $second  = 2;

	function run(){
		// your logic here
		Logger::log(get_class());
	}
}
