<?php

	/*
        Cambios en modelos

    */

    $paths = Files::recursiveGlob(MODELS_PATH . '/*.php');

    foreach ($paths as $path)
    {
        if (Strings::endsWith('MyModel.php', $path)){
            //continue;
        }

        $file = file_get_contents($path);
        
        Strings::replace('public static $active;', 'public static $is_active;', $file);
        Strings::replace('public static $locked;', 'public static $is_locked;', $file);
,
        $ok = file_put_contents($path, $file);
    }
