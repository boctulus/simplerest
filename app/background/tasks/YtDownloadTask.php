<?php

namespace simplerest\background\tasks;

use simplerest\core\libs\Task;
use simplerest\core\libs\System;

class YtDownloadTask extends Task
{ 
    static protected $priority = 10;
    static protected $exec_time_limit;
    static protected $memory_limit;
    static protected $overlap = false;

	function run(string $url){
		System::exec("yt $url");
	}
}
