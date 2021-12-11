<?php 

declare(strict_types=1);

namespace simplerest\controllers;

use simplerest\core\ConsoleController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\libs\Factory;
use simplerest\libs\DB;
use simplerest\libs\Files;
use simplerest\libs\Strings;

/*
    Arma el update para su distribución
*/
class PrepareUpdateController extends ConsoleController
{
    protected $ori, $dst;

    function __construct()
    {
        parent::__construct();     
        $this->setup();   
    }

    function setup(){
        $ori = '/home/www/simplerest';
        $dst = 'updates/2021-11-26-000000000001/files';
        //$dst = '/home/feli/Desktop/UPDATE';
        //$dst = '/home/www/html/simplerest-clone';

        // Solo para pruebas !!!!
        ////Files::delTree($dst);

        $str_files = <<<'FILES'
        config/constants.php
        app/core
        app/core/Controller.php
        app/exceptions
        app/helpers
        app/interfaces
        app/libs
        app/locale
        app/traits
        app/controllers/MigrationsController.php
        ;app/controllers/MyController.php
        ;app/controllers/MyApiController.php
        ;app/controllers/MyAuthController.php
        app/controllers/api/UserSpPermissions.php
        app/controllers/DownloadController.php
        app/controllers/api/Files.php
        app/controllers/api/Me.php
        app/models/main/CollectionsModel.php
        app/models/main/FolderOtherPermissionsModel.php
        app/models/main/FolderPermissionsModel.php
        app/models/main/FoldersModel.php
        app/models/main/RolesModel.php
        app/models/main/SpPermissionsModel.php
        app/models/main/UserTbPermissionsModel.php
        app/models/main/UserSpPermissionsModel.php
        docs
        ;app/views/MyView.php
        packages
        app/controllers/UpdateController.php     
        public/app.php
        .htaccess
        app/traits/ExceptionHandler.php
        FILES;

        $files = explode(PHP_EOL, $str_files);

        $except =  [
            'db_dynamic_load.php',
            'PrepareUpdateController.php',
            'docs/dev',
            'glob:*.zip'
        ];

        Files::copy($ori, $dst, $files, $except);
    }

   

}

