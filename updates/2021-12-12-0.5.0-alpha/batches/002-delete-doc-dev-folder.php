<?php

    use simplerest\libs\Files;
    use simplerest\libs\Strings;
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