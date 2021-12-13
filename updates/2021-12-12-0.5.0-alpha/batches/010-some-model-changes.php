<?php

    use simplerest\libs\Files;
    use simplerest\libs\Strings;
    use simplerest\libs\DB;
    use simplerest\libs\Schema;
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

                $file = file_get_contents($path);
                
                Strings::replace('public static $active;', 'public static $is_active;', $file);
                Strings::replace('public static $locked;', 'public static $is_locked;', $file);

                $ok = file_put_contents($path, $file);
                if (!$ok){
                    d("$file was changed");
                }
            }

            return true;
        }
    }

    
