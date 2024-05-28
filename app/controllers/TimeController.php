<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\libs\Date;
use simplerest\core\libs\Strings;
use simplerest\core\libs\TimeZone;
use simplerest\core\controllers\Controller;
use simplerest\models\main\TimezonesModel;


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

