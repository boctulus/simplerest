<?php

namespace Boctulus\Simplerest\Controllers\demos;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Libs\Date;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Controllers\Controller;

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

