<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;

class BackgroundService
{
    protected int $month;
    protected int $monthday;
	protected int $weekday;
	protected int $hour;
	protected int $minute;

    const SUN = 0;
    const MON = 1;
    const TUE = 2;
    const WED = 3;
    const THU = 4;
    const FRI = 5;
    const SAT = 6;

    function __construct() {    
        $this->start();
    }

	function start(){
    }

	function stop(){  
    }
}

