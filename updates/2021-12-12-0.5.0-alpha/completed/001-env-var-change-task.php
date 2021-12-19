<?php

    use simplerest\libs\Files;
    use simplerest\libs\Strings;
    use simplerest\core\interfaces\IUpdateBatch;

    /*
        Cambio DB_DATABASE por DB_NAME 
        como variable de entorno
    */

    class EnvVarChangeBatch implements IUpdateBatch
    {
        function run() : bool{
            $filenames = [
                '.env',
                'config/config.php',
                'app/helpers/db_dynamic_load.php'
            ];

            foreach ($filenames as $f){
                Files::replace(ROOT_PATH . $f, 'DB_DATABASE=', 'DB_NAME=');
                Files::replace(ROOT_PATH . $f, "env('DB_DATABASE')", "env('DB_NAME')");
            }

            return true;   
        }
    }