<?php

use simplerest\libs\Files;
use simplerest\libs\Strings;
use simplerest\core\interfaces\IUpdateBatch;
use simplerest\controllers\MigrationsController;

/*
    Run batches
*/

class __NAME__ implements IUpdateBatch
{
    function run() : ?bool{
        // ...
        
        return true;
    }
}