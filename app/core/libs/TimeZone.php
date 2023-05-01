<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Date;
use simplerest\core\libs\DB;

/*
    Calculo de fecha-hora teniendo en cuenta zonas horarias

    Utiliza una lista de ciudades en base de datos
    en vez de resolver exclusivamente TimeZones de PHP

    La mejor forma de hacerlo seria resolver todo con Timezones de PHP
    y tener en una base de datos a todas las ciudades mapeadas contra esos timezones

    Ej:

    Salta y Cordoba en Argentina siempre tienen el mismo TZ que Buenos Aires.
*/
class TimeZone
{
    protected $default_tz;

    function __construct()
    {
        $cfg = config();
        if (isset($cfg['DateTimeZone'])){
            $this->setDefault(Date::getGMTfromTimeZone($cfg['DateTimeZone']));
        }
    }

    function setDefault($gmt){
        $this->default_tz = $gmt;
    }
    
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

        return ($this->default_tz - $gmt);
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

        // la suma solo funcionara con $time numerico y no sexagecimal
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

        // la suma solo funcionara con $time numerico y no sexagecimal
        return Date::realTime($time + $diff);
    }

}

