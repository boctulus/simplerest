<?php

	$paths = Files::recursiveGlob(MODELS_PATH . '/*.php');

    foreach ($paths as $path){
        if (Strings::endsWith('MyModel.php', $path)){
            continue;
        }
    
        $stripped_path  = Strings::diff($path, MODELS_PATH);
        $folder = Strings::match($stripped_path, '~([a-z0-9_]+)/~');

        if (!empty($folder)){
            $file = file_get_contents($path);
            Strings::replace('namespace simplerest\models;', "namespace simplerest\models\\$folder;", $file);
            $ok = file_put_contents($path, $file);
        }             
    }