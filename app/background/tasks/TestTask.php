<?php

namespace Boctulus\Simplerest\Background\Tasks;

use Boctulus\Simplerest\Core\Libs\Task;

class TestTask extends Task
{ 
    static protected $priority = 10;
    static protected $exec_time_limit;
    static protected $memory_limit;
    static protected $overlap = false;

	function run(string $name, int $age){
		// your logic here
	}
}
