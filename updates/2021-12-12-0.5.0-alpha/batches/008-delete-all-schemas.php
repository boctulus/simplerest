<?php

    use simplerest\core\libs\Files;
    use simplerest\core\libs\Strings;
    use simplerest\core\libs\DB;
    use simplerest\core\interfaces\IUpdateBatch;

	/*
        Borrar todos los schemas de la vieja ubicación (dentro de models)

    */

    class ClearAllOldSchemasBatch implements IUpdateBatch
    {
        function run() : bool
        {
            $ok = Files::delTree(MODELS_PATH . 'schemas', true);
            d($ok, 'Schema directory cleared');

            return true;
        }
    }

    