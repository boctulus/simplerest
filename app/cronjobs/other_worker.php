<?php

use simplerest\core\libs\BackgroundService;

class OtherWorker extends BackgroundService 
{
	protected int $month;
    protected int $monthday;
	protected int $weekday;
	protected int $hour;
	protected int $minute;

	function start(){
		// your logic here
		d('Starting ' . get_class());
	}

	function stop(){
		// your logic here
	}
}
