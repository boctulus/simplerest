<?php

    use simplerest\libs\Files;
    use simplerest\controllers\MigrationsController;
    use simplerest\libs\Strings;
    use simplerest\controllers\UpdateController;
    use simplerest\controllers\MakeController;
    use simplerest\core\interfaces\IUpdateBatch;

    /*
        Run migrations
    */

    class MigrationsBatch implements IUpdateBatch
    {
        function run() : bool{
            $mgr = new MigrationsController();
            $mk = new MakeController();

            $tenant = 'main';

            //php com make schema migrations --from:main
            $mk->schema("migrations", "--from:main", "--force");
            
            // Debo correr solo las migraciones que están dentro del update
            $dir = UpdateController::$update_path . 'files/app/migrations/';
            $mgr->migrate("--to=$tenant", "--dir=" . $dir);

            return true;
        }
    }