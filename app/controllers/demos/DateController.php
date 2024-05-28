<?php

namespace simplerest\controllers\demos;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Date;
use simplerest\core\libs\Factory;
use simplerest\core\controllers\Controller;

class DateController extends Controller
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
    
    function realtime(){
        $time = 5;
        dd(Date::realTime($time), $time);

        $time = 23;
        dd(Date::realTime($time), $time);

        $time = 24;
        dd(Date::realTime($time), $time);

        $time = 28;
        dd(Date::realTime($time), $time);

        ///

        ///

        $time = '5:00';
        dd(Date::realTime($time), $time);

        $time = '23:00';
        dd(Date::realTime($time), $time);

        $time = '24:00';
        dd(Date::realTime($time), $time);

        $time = '28:00';
        dd(Date::realTime($time), $time);

        ///

        $time = '5:00:00';
        dd(Date::realTime($time), $time);

        $time = '06:00:00';
        dd(Date::realTime($time), $time);

        $time = '23:00:59';
        dd(Date::realTime($time), $time);

        $time = '24:00:03';
        dd(Date::realTime($time), $time);

        $time = '28:00:20';
        dd(Date::realTime($time), $time);

    }
}

