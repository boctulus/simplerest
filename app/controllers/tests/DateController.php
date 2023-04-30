<?php

namespace simplerest\controllers\tests;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Date;
use simplerest\core\libs\Factory;
use simplerest\controllers\MyController;

class DateController extends MyController
{  
    function test_gfg5()
    {
        // Esta semana 
        $date = at();

        $date = Date::nextNthWeekDay(1, $date);
        dd($date);

        // Semana del xxxx-xx-xx
        $date = Date::addDays($date, 7);
        dd($date);

        // Semana del xxxx-xx-xx
        $date = Date::addDays($date, 7);
        dd($date);

        // Semana del xxxx-xx-xx
        $date = Date::addDays($date, 7);
        dd($date);
    }
}

