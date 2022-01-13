<?php

use simplerest\core\libs\Files;
use simplerest\core\libs\Strings;
use simplerest\core\interfaces\IUpdateBatch;
use simplerest\controllers\MigrationsController;
use simplerest\controllers\UpdateController;

/*
    Run batches
*/

class NewCopyUpdateBatch implements IUpdateBatch
{
    function run() : ?bool{
        $ok = Files::copy(UpdateController::$update_path . 'files2', ROOT_PATH);
        return true;
    }
}