<?php

namespace Boctulus\Simplerest\Background\Tasks;

use Boctulus\Simplerest\Core\Libs\Task;
use Boctulus\Simplerest\Core\Libs\System;

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
