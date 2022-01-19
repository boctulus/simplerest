<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Date;
use simplerest\core\libs\BackgroundService;

class AsyncController extends MyController
{
    public function loop(string $task_filename)
    {   
        $path = CRONOS_PATH . $task_filename;

        $class_name = Strings::getClassNameByFileName($path);

        require_once $path;

        if (!class_exists($class_name)){
            throw new \Exception ("Class '$class_name' doesn't exist in $file");
        } 

        $task = new $class_name();
        d($class_name, 'Task name');

        if (! $task instanceof BackgroundService){
            throw new \Exception ("Class '$class_name' should be instance of BackgroundService");
        }

        $freq = $task::getFrequency();

        $mnth = $freq['month']    ?? -1;
        $mndy = $freq['monthday'] ?? -1;
        $wkdy = $freq['weekday']  ?? -1;
        $hour = $freq['hour']     ?? -1;
        $mins = $freq['minute']   ??  0;
        $secs = $freq['second']   ??  0;

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
                    $dm = Date::diffInSeconds(Date::nextNthMonthFirstDay($mnth));
                    //d($dm, 'Diff por $mnth');
                }
            }

            if (($wkdy !== -1)){
                if ($wkdy != $w){
                    $dw = Date::diffInSeconds(Date::nextNthWeekDay($wkdy));
                    //d($dw, 'Diff por $wkdy');
                }
            }

            if (($mndy !== -1)){
                if ($mndy != $d){
                    $dd = Date::diffInSeconds(Date::nextNthMonthDay($mndy));
                    //d($dd, 'Diff por $mndy');
                }
            }

            if (($hour !== -1)){
                if ($hour != $h){
                    if ($hour > $h){
                        $dh = ($hour - $h -1) * 3600 + (3600 -$s -($m * 60));
                    } else {
                        $dh = (24 - $h + $hour) * 3600 + (3600 -$s -($m * 60));
                    }
                    //d($dh, 'Diff por $h');
                }
            }

            if (($secs !== 0) || $mins !== 0){
                $ds = $secs + ($mins *60);
                //d($ds, 'Diff por $secs y $mins');
            }

            $diff = max($dm ?? 0, $dw ?? 0, $dd ?? 0, $dh ?? 0);
            //d($diff, 'Total diff en segundos')

            sleep($diff);
            $task->start();

            /*
                Si $mins == 0  &&  $secs == 0
                => ejecutar solo una vez
                
                Para esto, luego de ejecutarse, esperar 86400 segundos con lo cual ya no se cumplirá la condición 
                en ese día.
            */

            if ($mins == 0  &&  $secs == 0){
                sleep(86400);
            } else {
                sleep($ds);
            }


        } // end while
    }
}

