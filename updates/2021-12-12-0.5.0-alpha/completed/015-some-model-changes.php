<?php

    use simplerest\libs\Files;
    use simplerest\libs\Strings;
    use simplerest\libs\DB;
    use simplerest\libs\StdOut;
    use simplerest\core\interfaces\IUpdateBatch;

	/*
        Cambios en modelos

    */

    class LockedAndActivePropChangeBatch implements IUpdateBatch
    {
        function run() : bool
        {
            $paths = Files::recursiveGlob(MODELS_PATH . '/*.php');

            foreach ($paths as $path)
            {
                if (Strings::endsWith('MyModel.php', $path)){
                    continue;
                }

                if (!file_exists($path)){
                    StdOut::pprint("File $path does not exist");
                    return false;
                }

                $file = file_get_contents($path);
                
                StdOut::pprint("Processing $path");

                // Deberían ser expresiones regulares y considerar [; espacio y tab]
                Strings::replace('public static $active', 'public static $is_active', $file);
                Strings::replace('public static $locked', 'public static $is_locked', $file);

                $bytes = file_put_contents($path, $file);
                
                if (!$bytes){
                    StdOut::pprint("Fail to write $path");
                    return false;
                }
            }

            return true;
        }
    }

    
