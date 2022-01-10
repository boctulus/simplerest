<?php 

declare(strict_types=1);

namespace simplerest\controllers;

use simplerest\core\ConsoleController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\libs\Factory;
use simplerest\libs\DB;
use simplerest\libs\Files;
use simplerest\libs\StdOut;
use simplerest\libs\Update;

/*
    Arma el update para su distribución
*/
class PrepareUpdateController extends ConsoleController
{
    protected $last_update_dir;
    protected $version;

    function __construct()
    {
        parent::__construct();     
        $this->setup();   
    }

    function setup(){
        $this->last_update_dir = Update::getLastVersionDirectory();
        $this->version         = substr($this->last_update_dir, 11);
    }

    function index(){
        StdOut::pprint("Do you need help? You can copy or zip");
    }

    function copy(){
        $ori = '/home/www/simplerest';
        $dst = "updates/{$this->last_update_dir}/";  

        Files::copy($dst, ROOT_PATH, ['version.txt']); 

        $str_files = <<<'FILES'
        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;; DOC
        docs

        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;; CORE 
        app/core
        ;app/core/api/v1/ApiController.php
        ;app/core/Model.php
        
        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;; VARIOS
        app/helpers/db.php
        app/libs/Strings.php   
        app/libs/VarDump.php    
       
        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;; MIGRATIONS
        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;app/migrations/2021_11_20_33704817_files.php
        ;app/migrations/2021_12_07_35172655_files.php   // <-- quedó pendiente
        ;app/migrations/compania/2021_10_28_31693371_tbl_concepto_nomina.php
        FILES;

        $files = explode(PHP_EOL, $str_files);

        $except =  [
            'initial_file_copy.batch',
            'db_dynamic_load.php',
            'PrepareUpdateController.php',
            'docs/dev',
            'glob:*.zip',
            'Mails.php',
            'PASARELAS DE PAGO.txt'
        ];

        Files::copy($ori, $dst . 'files', $files, $except);
    }

    function zip(){
        Update::compress($this->last_update_dir);
    }

}

