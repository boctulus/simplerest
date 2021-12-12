<?php

    use simplerest\libs\Files;
    use simplerest\controllers\MigrationsController;
    use simplerest\libs\Strings;

    /*
        Run migrations

        Debería correr solo las migraciones que están dentro del update y 
        estas deberían moverse a migrations/update
        ejecutarse y...
        volver a ser movidas al root de migrations
    */

    $mgr = new MigrationsController();

    $tenant = 'main';
    //StdOut::hideResponse();

    $mgr->migrate("--to=$tenant");