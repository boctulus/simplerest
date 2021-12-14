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

    // compress an update
    static function compress(?string $update_dir = null){
        if (is_null($update_dir)){
            $update_dir = Update::getLastVersionDirectory();

            if (!Files::isAbsolutePath($update_dir)){
                $update_dir = UPDATE_PATH . $update_dir;
            }
        }
        
        $update_dir = UPDATE_PATH . $update_dir . DIRECTORY_SEPARATOR;

        $tmp_dst = '/tmp/simplerest/';
        
        if (is_dir($tmp_dst)){
            Files::delTree($tmp_dst, false); 
        } else {
            mkdir($tmp_dst);
        }

        if (is_dir($tmp_dst . 'updates')){
            Files::delTree($tmp_dst . 'updates', false); 
        } else {
            mkdir($tmp_dst . 'updates');
        }

        Files::copy(UPDATE_PATH, $tmp_dst . 'updates', [ basename($update_dir) ]);
        Files::copy(ROOT_PATH, $tmp_dst, ['app/controllers/UpdateController.php']);

        Files::zip($tmp_dst, UPDATE_PATH . 'update-' . basename($update_dir) . '.zip', [
            "completed"
        ]);
    }
}

