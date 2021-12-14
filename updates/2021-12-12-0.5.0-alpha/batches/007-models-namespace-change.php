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
                $class_name = Strings::getClassNameByFileName($path);
                $_cl = explode('\\', $class_name);
                $class_name  = end($_cl);
                $schema_name = trim(str_replace('Model', 'Schema', $class_name));

                $file = file_get_contents($path);

                $file  = str_replace('namespac'. 'e simplerest\models;', 'namespac'. "e simplerest\models\\$folder;", $file);
                
                $file  = str_replace('use simplerest\\models\schemas\\',  'use simplerest\\schemas\\', $file);
                $file  = str_replace('use simplerest\\schemas\\'.$schema_name. ';', 'use simplerest\\schemas\\'.$folder . '\\'. $schema_name. ';', $file);    

                // d($file);
                // exit;

            
                $bytes = file_put_contents($path, $file);

                if (!$bytes){
                    return false;
                }
            }
        }

        return true;
    }
}