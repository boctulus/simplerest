<?php

    use simplerest\core\libs\Files;
    use simplerest\core\libs\Strings;
    use simplerest\core\interfaces\IUpdateBatch;

    /*
            Borrar la carpeta /docs/dev

    */

    class DelDevNotesBatch implements IUpdateBatch
    {
        function run() : bool{
            $ok = Files::delTree(ROOT_PATH . 'docs/dev');

            return true;
        }
    }