<?php

namespace simplerest\libs;

use simplerest\core\Model;
use simplerest\libs\DB;
use simplerest\libs\Factory;

class Update
{
    static function getVersion(string $version) : Array {
        $regex = '/^(?P<major>0|[1-9]\d*)\.(?P<minor>0|[1-9]\d*)\.(?P<patch>0|[1-9]\d*)(?:-(?P<prerelease>(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+(?P<buildmetadata>[0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$/';

        if (!preg_match($regex, $version, $matches)){
            throw new \InvalidArgumentException("Version '$version' has incorrect format for semantic versioning");
        }

        $arr = [
            'major' => $matches['major'],
            'minor' => $matches['minor'],
            'patch' => $matches['patch'],
            'pre'   => $matches['prerelease'] ?? null
        ];

        return $arr;
    }

    static function getLastVersionDirectory() : string {        
        $last_ver = [
            'major' => null,
            'minor' => null,
            'patch' => null,
            'pre'   => null
        ];

        $last_ver_dir = null;

        foreach (new \DirectoryIterator(UPDATE_PATH) as $fileInfo) {
            if($fileInfo->isDot() || !$fileInfo->isDir()) continue;
            
            $dir = $fileInfo->getBasename();
            $ver = substr($dir, 11);
            
            $current = static::getVersion($ver);

            if ($current > $last_ver){
                $last_ver     = $current; 
                $last_ver_dir = $dir;
            }
        }

        return $last_ver_dir;
    }

    static function getLastVersionInDirectories() : string {      
        return substr(static::getLastVersionDirectory(), 11);
    }

    static function getLastInstalledVersion() : string {
        $cur_ver = file_get_contents(ROOT_PATH . 'version.txt');
        return $cur_ver;
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
        //Files::copy(ROOT_PATH, $tmp_dst, ['app/controllers/UpdateController.php']);

        Files::zip($tmp_dst, UPDATE_PATH . 'update-' . basename($update_dir) . '.zip', [
            "completed"
        ]);
    }
}

