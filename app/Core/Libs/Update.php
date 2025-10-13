<?php declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Zip;

class Update
{
    static function compareVersions(Array $v1, Array $v2) : int {
        $v1_no_pre_release = [
            $v1['major'],
            $v1['minor'],
            $v1['patch']
        ];

        $v2_no_pre_release = [
            $v2['major'],
            $v2['minor'],
            $v2['patch']
        ];

        if ($v1_no_pre_release == $v2_no_pre_release){
            $v1['pre'] = $v1['pre'] ?? null;
            $v2['pre'] = $v2['pre'] ?? null;

            if (is_null($v1['pre']) && is_null($v2['pre'])){
                return 0;
            }

            if (is_null($v1['pre'])){
                return 1;
            }

            if (!is_null($v1['pre']) && !is_null($v2['pre'])){
                if ($v1['pre'] > $v2['pre']){
                    return 1;
                }

                if ($v1['pre'] < $v2['pre']){
                    return -1;
                }

                return 0;
            }

            return -1;
        } else {
            if ($v1 == $v2){
                return 0;
            }

            if ($v1 > $v2){
                return 1;
            }

            return -1;
        }
    }

    static function compareVersionStrings(string $v1, string $v2) : int {
        $a1 = static::getVersion($v1);
        $a2 = static::getVersion($v2);

        return static::compareVersions($a1, $a2);
    }

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

            // necesito función de comparación
            if (static::compareVersions($current, $last_ver)>0){
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
        if (!file_exists(ROOT_PATH . 'version.txt')){
            return '0.5.0-alpha.1';
        }

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

        ZipManager::zip($tmp_dst, UPDATE_PATH . 'update-' . basename($update_dir) . '.zip', [
            "completed"
        ]);
    }
}

