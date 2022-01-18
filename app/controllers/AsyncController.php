<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Date;
use simplerest\core\libs\BackgroundService;

class AsyncController extends MyController
{
    public function loop(string $task_filename, int $mnth, int $mndy, int $wkdy, int $hour, int $mins, int $secs)
    {   
        $path = CRONOS_PATH . $task_filename;

        $class_name = Strings::getClassNameByFileName($path);

        require_once $path;

        if (!class_exists($class_name)){
            throw new \Exception ("Class '$class_name' doesn't exist in $file");
        } 

        $task = new $class_name();

        if (! $task instanceof BackgroundService){
            throw new \Exception ("Class '$class_name' should be instance of BackgroundService");
        }

        while (true)
        {
            $M = (int) datetime('n');
            $d = (int) datetime('j');
            $w = (int) datetime('w');
            $h = (int) datetime('G');
            $m = (int) datetime('i');
            $s = (int) datetime('s');

            if (($mnth !== -1)){
                if ($mnth != $M){
                    $diff = Date::diffInSeconds('fecha más próxima con ese mes');
                    sleep($diff);
                }
            }

            if (($wkdy !== -1)){
                if ($wkdy != $w){
                    $diff = Date::diffInSeconds('fecha más próxima con ese día de la semana');
                    sleep($diff);
                }
            }

            if (($d !== -1)){
                if ($mndy != $d){
                    $diff = Date::diffInSeconds('fecha del más proximo monthDay'); /// completar
                    sleep($diff);
                }
            }

            if (($h !== -1)){
                if ($hour != $h){
                    //  nextDayBegining
                    $diff = Date::diffInSeconds('00:00:00 del siguiente día');
                    sleep($diff);
                }
            }

            if (($secs !== 0) || $mins !== 0){
                $task->start();
                sleep($secs + ($mins *60));
            }

        } // end while
    }
}

