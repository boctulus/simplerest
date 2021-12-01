<?php

        /*
            Cambio DB_DATABASE por DB_NAME 
            como variable de entorno
        */

        $filenames = [
            '.env',
            'config/config.php',
            'app/helpers/db_dynamic_load.php'
        ];

        foreach ($filenames as $f){
            Files::replace(ROOT_PATH . $f, 'DB_DATABASE=', 'DB_NAME=');
            Files::replace(ROOT_PATH . $f, "env('DB_DATABASE')", "env('DB_NAME')");
        }
