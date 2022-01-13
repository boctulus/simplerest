<?php

    use simplerest\core\libs\Files;
    use simplerest\core\libs\Strings;
    use simplerest\core\interfaces\IUpdateBatch;

    /*
        MakeControllerBase::hideResponse();
        MigrationsController::hideResponse();

        por 

        \simplerest\core\libs\StdOut::hideResponse();
    */

    class UseOfStdOutBatch implements IUpdateBatch
    {
        function run() : bool
        {
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
                                '\simplerest\core\libs\StdOut::hideResponse();', $file);

                Strings::replace('MigrationsController::hideResponse();', 
                                '\simplerest\core\libs\StdOut::hideResponse();', $file);

                file_put_contents($path, $file);
            }
            
            return true;
        }
    }
    

