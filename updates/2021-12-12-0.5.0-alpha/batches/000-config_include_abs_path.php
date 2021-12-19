<?php

use simplerest\libs\Files;
use simplerest\libs\Strings;
use simplerest\core\interfaces\IUpdateBatch;
use simplerest\controllers\MigrationsController;

/*
    Run batches
*/

class ConfigIncludeAbsPathUpdateBatch implements IUpdateBatch
{
    function run() : ?bool{
    	$path = CONFIG_PATH . 'config.php';

        if (!file_exists($path)){
            throw new \Exception("File $path does not exist");
        }

        $file  = file_get_contents($path);

       	$file  = str_replace("require_once 'constants.php';", "require_once __DIR__ . '/constants.php';", $file);
       	$bytes = file_put_contents($path, $file);
        
        return (bool) $bytes;
    }
}