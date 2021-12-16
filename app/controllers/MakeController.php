<?php

namespace simplerest\controllers;

use simplerest\core\MakeControllerBase;
use simplerest\libs\DB;
use simplerest\libs\Files;
use simplerest\libs\StdOut;
use simplerest\libs\Strings;


class MakeController extends MakeControllerBase
{
    function __construct()
    {
        parent::__construct();
    }
    
    /*
        Here you can add your own commands for "make"
    */

    /*
        Podría poder especificar:

            --major | -x
            --minor | -y
            --patch | -z

            --next-major | -nx
            --next-minor | -ny
            --next-patch | -nz

        Tener en cuenta que:

        1.2.0-a.1  = 1.2.0.1
        1.2.0-b.2  = 1.2.1.2
        1.2.0-rc.3 = 1.2.2.3

        https://en.wikipedia.org/wiki/Software_versioning


        Además,.....

            --prepare | -p

        => invocaría a PrepareUpdateController

    */
    function update(string $version, ...$opt) {
        Files::writableOrFail(UPDATE_PATH);

        $folder = substr(at(), 0, 10) . '-' . $version . DIRECTORY_SEPARATOR;
        $path   = UPDATE_PATH . $folder;

        /*
            Version validation  

            Usar expresiones publicadas aquí: <--------
            https://semver.org/lang/es/
        */
        if (!preg_match('/([0-9]+).([0-9]+).([0-9]+)([-]?)([a-z]+)?([0-9])?/', $version)){
            throw new \InvalidArgumentException("Version '$version' has incorrect format for semantic versioning");
        }

        $dirs = [];
        foreach (new \DirectoryIterator(UPDATE_PATH) as $fileInfo) {
            if($fileInfo->isDot() || !$fileInfo->isDir()) continue;
            
            $dirs[] = $fileInfo->getBasename();
        }

        /*
            La forma de ordenamiento no es del todo correcta !

            1.0.1-beta < 1.0.11
        */
        sort($dirs);

        $last_ver = substr(end($dirs), 11);
        
        if ($folder < $last_ver){            
            throw new \InvalidArgumentException("Version '$version' can not be inferior to ". $last_ver);
        }

        Files::mkDirOrFail($path);
        Files::mkDirOrFail($path . 'files');
        Files::mkDirOrFail($path . 'batches');
        Files::mkDirOrFail($path . 'completed');

        file_put_contents($path  . 'version.txt', $version);

        if (!file_exists($path  . 'description.txt')){
            file_put_contents($path  . 'description.txt', file_get_contents(CORE_PATH . 'templates/description.txt'));
        }
    }


    function batch(string $name, ...$opt) {
        if (strlen($name)<5){
            throw new \InvalidArgumentException("Batch name is too short. Please use 5 or more chars");
        }

        $name = str_replace(' ', '_', $name);
        $name = str_replace('-', '_', $name);

        $dirs = [];
        foreach (new \DirectoryIterator(UPDATE_PATH) as $fileInfo) {
            if($fileInfo->isDot() || !$fileInfo->isDir()) continue;
            
            $dirs[] = $fileInfo->getBasename();
        }

        sort($dirs);

        $folder   = end($dirs);
        $path     = UPDATE_PATH . $folder . DIRECTORY_SEPARATOR . 'batches' . DIRECTORY_SEPARATOR;

        $last_ver = substr($folder, 11);
       
        $date = date("Ymd");
        $secs = time() - 1603750000;
        $filename = $date . '-'. $secs . '-' . Strings::camelToSnake($name) . '.php';

        if (file_exists($path . $filename)){
            throw new \InvalidArgumentException("File $filename already exists in $path");
        }

        $batch = file_get_contents(TEMPLATES_PATH . 'UpdateBatch.php');
        Strings::replace('__NAME__', Strings::snakeToCamel($name) . 'UpdateBatch', $batch);
        

        $ok = file_put_contents($path . $filename, $batch);

        Files::writableOrFail($path);
        
        if ($ok){
            StdOut::pprint('File ' . $path . $filename . ' was created');
        } else {
            StdOut::pprint('Error trying to write file ' . $path . $filename);
        }        
    }
}