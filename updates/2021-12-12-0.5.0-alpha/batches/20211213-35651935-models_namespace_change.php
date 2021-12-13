<?php

use simplerest\libs\Files;
use simplerest\libs\Strings;
use simplerest\core\interfaces\IUpdateBatch;
use simplerest\controllers\MigrationsController;

/*
    Run batches
*/

class ModelsNamespaceChangeUpdateBatch implements IUpdateBatch
{
    function run() : ?bool{
        $paths = Files::recursiveGlob(MODELS_PATH . '/*.php');

        foreach ($paths as $path){
        	if (Strings::endsWith('MyModel.php', $path)){
                continue;
            }

            $stripped_path  = Strings::diff($path, MODELS_PATH);
            $folder = Strings::match($stripped_path, '~([a-z0-9_]+)/~');

            if (!empty($folder)){
            	$file = file_get_contents($path);
            	$file = str_replace('namespac'. 'e simplerest\models;', "namespac'. 'e simplerest\models\\$folder;", $file);
            	$ok = file_put_contents($path, $file);
            }
        }

        return true;
    }
}