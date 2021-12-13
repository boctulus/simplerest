<?php

    use simplerest\libs\Files;
    use simplerest\libs\Strings;

    /*
        MakeControllerBase::hideResponse();
        MigrationsController::hideResponse();

        por 

        \simplerest\libs\StdOut::hideResponse();
    */

    $patts = [
        HELPERS_PATH . '/*.php',
        MODELS_PATH . '/*.php',
        CONTROLLERS_PATH . '/*.php'
    ];

    $files = [];

    foreach ($patts as $p){
        $files = array_merge($files, Files::recursiveGlob($p));
    }

    foreach ($files as $path){
        $file = file_get_contents($path);

        if ($file == __FILE__){
            continue;
        }

        Strings::replace('MakeControllerBase::hideResponse();', 
                        '\simplerest\libs\StdOut::hideResponse();', $file);

        Strings::replace('MigrationsController::hideResponse();', 
                        '\simplerest\libs\StdOut::hideResponse();', $file);

        file_put_contents($path, $file);
    }

