<?php

    use simplerest\libs\Files;
    use simplerest\libs\Strings;

	/*
        Cambios en modelos

    */

    $paths = Files::recursiveGlob(MODELS_PATH . '/*.php');

    foreach ($paths as $path)
    {
        if (Strings::endsWith('MyModel.php', $path)){
            continue;
        }

        $file = file_get_contents($path);
    
        $file = preg_replace('~new ([^(]+)~', "$1::class", $file);
        $file = str_replace('::class()', '::class', $file);

        /*
             + otros cambios
        */

        // Agrego use simplerest\models\MyModel
        if (!Strings::contains('use simplerest\models\MyModel;', $file)){
            $lines = explode(PHP_EOL, $file);

            foreach ($lines as $ix => $line){
                $line = trim($line);

                if (Strings::startsWith('use ', $line)){
                    $lines[$ix] = 'use simplerest\models\MyModel;' . PHP_EOL . $line ;
                    break;
                }
            }

            $file = implode(PHP_EOL, $lines); 
        }

        $ok = file_put_contents($path, $file);
        d($ok, 'done');
    }
