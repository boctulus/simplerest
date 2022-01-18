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
                    $dm = Date::diffInSeconds(Date::nextNthMonthFirstDay($mnth));
                    d($dm, 'Diff por $mnth');
                    //sleep($dm,);
                }
            }

            if (($wkdy !== -1)){
                if ($wkdy != $w){
                    $dw = Date::diffInSeconds(Date::nextNthWeekDay($wkdy));
                    d($dw, 'Diff por $wkdy');
                    //sleep($dw);
                }
            }

            if (($mndy !== -1)){
                if ($mndy != $d){
                    $dd = Date::diffInSeconds(Date::nextNthMonthDay($mndy));
                    d($dd, 'Diff por $mndy');
                    //sleep($dd);
                }
            }

            if (($hour !== -1)){
                if ($hour != $h){
                    if ($hour > $h){
                        $dh = ($hour - $h -1) * 3600;
                    } else {
                        $dh = (24 - $h + $hour -2) * 3600;
                    }

                    d($dh, 'Diff por $h');
                    //sleep($dh);
                }
            }

            if (($secs !== 0) || $mins !== 0){
                $task->start();

                $ds = $secs + ($mins *60);

                d($ds, 'Diff por $secs y $mins');
                //sleep($ds);
            }

            $diff = max($dm ?? 0, $dw ?? 0, $dd ?? 0, $dh ?? 0, $ds ?? 0);
            d($diff, 'Total diff en segundos');
            sleep($diff);

        } // end while
    }
}

