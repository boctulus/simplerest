<?php

namespace simplerest\controllers;

use simplerest\core\MakeControllerBase;
use simplerest\libs\DB;
use simplerest\libs\Files;
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

    function update(string $version, ...$opt) {
        $folder = substr(at(), 0, 10) . '-' . $version . DIRECTORY_SEPARATOR;
        
        if (!preg_match('/([0-9]+).([0-9]).([0-9]+)([-]?)([a-z]+)?([0-9])?/', $version)){
            throw new \InvalidArgumentException("Version '$version' has incorrect format for semantic versioning");
        }

        $path = UPDATE_PATH . $folder;

        Files::mkDirOrFail($path);
        Files::mkDirOrFail($path . 'files');
        Files::mkDirOrFail($path . 'batches');

        file_put_contents($path  . 'version.txt', $version);
        file_put_contents($path  . 'description.txt', PHP_EOL);
    }

}