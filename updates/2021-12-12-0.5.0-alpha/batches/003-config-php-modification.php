<?php

    use simplerest\libs\Files;
    use simplerest\libs\Strings;
    use simplerest\core\interfaces\IUpdateBatch;

	/*
         Modificar el config.php agregando la nueva sección para tenant groups
    */

    class ConfigFileModificationBatch implements IUpdateBatch
    {
        function run() : bool {   
            $new = "
            'tentant_groups' => [
                'legion' => [
                    'db_[0-9]+',
                    'db_legion',
                    'db_flor'
                ]
            ],";

            $path = CONFIG_PATH . 'config.php';

            $file = file_get_contents($path);

            if (!Strings::contains('tentant_groups', $file)){
                $needle = "'db_connection_default' => 'main',";
                $pos = strpos($file, $needle);
                $pos += strlen($needle);

                $left  = Strings::left($file,  $pos);
                $right = Strings::right($file, $pos);

                $file = $left . PHP_EOL . Strings::tabulate($new, -4) . $right;

                print_r("Updating config.php\r\n");
                file_put_contents($path, $file);  
            }
            
            return true;
        }
    }
        