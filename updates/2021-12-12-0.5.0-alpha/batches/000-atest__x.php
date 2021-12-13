<?php

use simplerest\libs\Files;
use simplerest\libs\Strings;
use simplerest\core\interfaces\IUpdateBatch;
use simplerest\controllers\MigrationsController;

/*
    Run batches
*/

class TestXUpdateBatch implements IUpdateBatch
{
    function run() : ?bool{
        // ...
        
    	d('OK');

        return true;
    }
}