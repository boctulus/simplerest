<?php

namespace simplerest\libs;

use simplerest\core\Model;
use simplerest\libs\DB;
use simplerest\libs\Factory;

class Update
{
    static function getLastVersionDirectory(){
        $dirs = [];
        foreach (new \DirectoryIterator(UPDATE_PATH) as $fileInfo) {
            if($fileInfo->isDot() || !$fileInfo->isDir()) continue;
            
            $dirs[] = $fileInfo->getBasename();
        }

        /*
            La forma de ordenamiento no es del todo correcta !

            1.0.1-beta < 1.0.11

            porque ...

            1.0.1-beta = 1.0.1.1 
        */

        sort($dirs);
        $last_ver_dir = end($dirs);

        return $last_ver_dir;
    }

    // compress last update
    static function zip(?string $last_update_dir = null){
        if (is_null($last_update_dir)){
            $last_update_dir = Update::getLastVersionDirectory();

            if (!Files::isAbsolutePath($last_update_dir)){
                $last_update_dir = UPDATE_PATH . $last_update_dir;
            }
        }
        
        $last_update_dir = UPDATE_PATH . $last_update_dir . DIRECTORY_SEPARATOR;

        d($last_update_dir);
    }
}

