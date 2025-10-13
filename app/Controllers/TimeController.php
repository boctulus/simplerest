<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Date;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\TimeZone;
use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Models\main\TimezonesModel;


class TimeController extends Controller
{    
    /*
        Convertir por ejemplo de hora de Chile a horario local
    */
    function convertFrom(string $city, $time = null, $time_2 = null)
    {
        return (new TimeZone)->convertFrom($city, $time, $time_2);
    }

    /*
        Convertir por ejemplo de horario local a hora de Chile
    */
    function convertTo(string $city, $time = null, $time_2 = null)
    {
        return (new TimeZone)->convertTo($city, $time, $time_2);
    }
}

