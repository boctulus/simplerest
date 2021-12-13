<?php

    use simplerest\libs\Files;
    use simplerest\controllers\MigrationsController;
    use simplerest\libs\Strings;
    use simplerest\controllers\UpdateController;

    /*
        Run migrations
    */

    $mgr = new MigrationsController();

    $tenant = 'main';
    //StdOut::hideResponse();

    // Debo correr solo las migraciones que están dentro del update
    $dir = UpdateController::$update_path . 'files/app/migrations/';
    $mgr->migrate("--to=$tenant", "--dir=" . $dir);