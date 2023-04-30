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
*/
class TimeController extends MyController
{
    static $zonas_horarias;        

    function parse(){
        $filename = ETC_PATH . 'times_zones.txt';
        $file     = file_get_contents($filename);
        $rows     = Strings::lines($file, true, false);

        foreach ($rows as $row){
            list($city, $gmt) = explode(' => ', $row);

            $id = (new TimezonesModel(true))->insert([
                'city' => $city,
                'gmt'  => $gmt
            ]);

            dd($gmt, $city);

        }

    }

    function convert($country, $time, $time_2 = null)
    {

    }
}

