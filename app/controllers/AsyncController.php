<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\libs\Strings;
use simplerest\core\libs\BackgroundService;

class AsyncController extends MyController
{
    public function loop(string $task_filename, int $every_seconds)
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

        while (true){
            $task->start();
            sleep($every_seconds);
        }
    }
}

