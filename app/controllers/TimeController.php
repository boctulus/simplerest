<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\libs\Date;
use simplerest\core\libs\Files;
use simplerest\core\libs\Strings;
use simplerest\controllers\MyController;
use simplerest\models\main\TimezonesModel;

/*
    Calculo de fecha-hora teniendo en cuenta zonas horarias

    Utiliza una lista de ciudades en base de datos
    en vez de los TimeZones de PHP
*/
class TimeController extends MyController
{
    static $default_tz = 8; // Filipinas, Singapur, Taiwan
    static $time_zones;      
    
    /*
        De hora de la ciudad especificada a la local
    */
    protected function getDiff(string $city){
        $city = ucwords(strtolower($city));

        $gmt = DB::table('timezones')
        ->where([
            'city' => $city
        ])
        ->value('gmt', 'float');

        if ($gmt === false){
            return false;
        }

        return (static::$default_tz - $gmt);
    }


    /*
        Convertir por ejemplo de hora de Chile a horario local
    */
    function convertFrom(string $city, $time = null, $time_2 = null)
    {
        $diff = $this->getDiff($city);

        if ($diff === false){
            return("Ciudad \"$city\" no encontrada");
        }

        if ($time_2 != null){
            return [ Date::realTime($time + $diff), Date::realTime($time_2 + $diff) ];
        }   

        return Date::realTime($time + $diff);
    }

    /*
        Convertir por ejemplo de horario local a hora de Chile
    */
    function convertTo(string $city, $time = null, $time_2 = null)
    {
        $diff = $this->getDiff($city) * -1;

        if ($diff === false){
            return("Ciudad \"$city\" no encontrada");
        }

        if ($time_2 != null){
            return [ Date::realTime($time + $diff), Date::realTime($time_2 + $diff) ];
        }   

        return Date::realTime($time + $diff);
    }
}

