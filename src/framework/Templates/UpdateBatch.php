<?php

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Interfaces\IUpdateBatch;
use Boctulus\Simplerest\Controllers\MigrationsController;

/*
    Run batches
*/

class NAME__ implements IUpdateBatch
{
    function run() : ?bool{
        // ...
        
        return true;
    }
}