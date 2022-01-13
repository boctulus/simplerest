<?php

    use simplerest\core\libs\Files;
    use simplerest\controllers\MigrationsController;
    use simplerest\core\libs\Strings;
    use simplerest\controllers\UpdateController;
    use simplerest\core\interfaces\IUpdateBatch;

    /*
        Run migrations
    */

    class MigrationsBatch implements IUpdateBatch
    {
        function run() : bool{
            $mgr = new MigrationsController();

            $tenant = 'main';
            //StdOut::hideResponse();

            // Debo correr solo las migraciones que están dentro del update
            $dir = UpdateController::$update_path . 'files/app/migrations/';
            $mgr->migrate("--to=$tenant", "--dir=" . $dir);

            return true;
        }
    }