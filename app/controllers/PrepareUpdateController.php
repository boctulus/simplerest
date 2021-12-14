<?php 

declare(strict_types=1);

namespace simplerest\controllers;

use simplerest\core\ConsoleController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\libs\Factory;
use simplerest\libs\DB;
use simplerest\libs\Files;
use simplerest\libs\Update;

/*
    Arma el update para su distribución
*/
class PrepareUpdateController extends ConsoleController
{
    function __construct()
    {
        parent::__construct();     
        $this->setup();   
    }

    function setup(){
        $last_update_dir = Update::getLastVersionDirectory();
        $last_ver        = substr($last_update_dir, 11);
        
        
        $ori = '/home/www/simplerest';
        $dst = "updates/$last_update_dir/";  

        Files::copy($dst, ROOT_PATH, ['version.txt']); 

        $str_files = <<<'FILES'
        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;; CONFIG
        config/constants.php
        
        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;; CORE 
        app/core
        app/core/Controller.php

        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;; VARIOS
        app/exceptions
        app/helpers
        app/interfaces
        app/libs
        app/locale
        app/traits
        app/traits/ExceptionHandler.php
        packages
        docs
        public/app.php
        .htaccess

        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;; CONTROLLERS
        app/controllers/UpdateController.php   
        app/controllers/MigrationsController.php
        ;app/controllers/MyController.php
        ;app/controllers/MyApiController.php
        ;app/controllers/MyAuthController.php
        app/controllers/api/UserSpPermissions.php
        app/controllers/DownloadController.php
        
        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;; APIS
        app/controllers/api/Files.php
        app/controllers/api/Me.php
        
        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;; MODELS
        app/models/main/CollectionsModel.php
        app/models/main/FolderOtherPermissionsModel.php
        app/models/main/FolderPermissionsModel.php
        app/models/main/FoldersModel.php
        app/models/main/RolesModel.php
        app/models/main/SpPermissionsModel.php
        app/models/main/UserTbPermissionsModel.php
        app/models/main/UserSpPermissionsModel.php
        app/models/main/EmailNotificationsModel.php
        
        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;; VIEWS
        ;app/views/MyView.php    
          
        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;; MIGRATIONS
        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;app/migrations/2021_11_20_33704817_files.php
        ;app/migrations/2021_12_07_35172655_files.php   // <-- quedó pendiente
        FILES;

        $files = explode(PHP_EOL, $str_files);

        $except =  [
            'db_dynamic_load.php',
            'PrepareUpdateController.php',
            'docs/dev',
            'glob:*.zip'
        ];

        Files::copy($ori, $dst . 'files', $files, $except);

        Update::compress($last_update_dir);
    }
   

}

