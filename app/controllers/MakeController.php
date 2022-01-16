<?php

namespace simplerest\controllers;

use simplerest\core\controllers\MakeControllerBase;
use simplerest\core\libs\DB;
use simplerest\core\libs\Files;
use simplerest\core\libs\StdOut;
use simplerest\core\libs\Strings;
use simplerest\libs\Update;


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

        $new_ver = Update::getVersion($version);

        /*
            Previous
        */
       
        $ver_str = file_get_contents(ROOT_PATH . 'version.txt');
        $cur_ver = Update::getVersion($ver_str);

        if ($new_ver <= $cur_ver){            
            StdOut::pprint("Version '$version' should be superior to ". $ver_str);
            exit(1);
        }


        if (is_dir($path)){
            StdOut::pprint("Scaffolding for update version '$version' already exists");
            exit(1);
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
            StdOut::pprint("Batch name is too short. Please use 5 or more chars");
            exit(1);
        }

        $name = str_replace(' ', '_', $name);
        $name = str_replace('-', '_', $name);

    
        $folder   = Update::getLastVersionDirectory();
        $path     = UPDATE_PATH . $folder . DIRECTORY_SEPARATOR . 'batches' . DIRECTORY_SEPARATOR;

        $date = date("Ymd");
        $secs = time() - 1603750000;
        $filename = $date . '-'. $secs . '-' . Strings::camelToSnake($name) . '.php';

        if (file_exists($path . $filename)){
            StdOut::pprint("File $filename already exists in $path");
            exit(1);
        }

        $batch = file_get_contents(TEMPLATES_PATH . 'UpdateBatch.php');
        Strings::replace('__NAME__', Strings::snakeToCamel($name) . 'UpdateBatch', $batch);
        
        $ok = file_put_contents($path . $filename, $batch);

        Files::writableOrFail($path);
        
        if ($ok){
            StdOut::pprint('File ' . $path . $filename . ' was created');
        } else {
            StdOut::pprint('Error trying to write file ' . $path . $filename);
            exit(1);
        }        
    }
}

