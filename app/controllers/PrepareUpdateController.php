<?php 

declare(strict_types=1);

namespace simplerest\controllers;

use simplerest\core\controllers\ConsoleController;
use simplerest\core\libs\Files;
use simplerest\core\libs\StdOut;
use simplerest\core\libs\Update;
use simplerest\core\libs\Strings;

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
        StdOut::pprint("Do you need help? You can copy | zip");
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
        
        ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
        ;; VARIOS   
        public/app.php
        config/constants.php
       
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
            'PrepareUpdateController.php',
            'docs/dev',
            'glob:*.zip',
            'PASARELAS DE PAGO.txt',
            'yakpro-po'
        ];

        Files::copy($ori, $dst . 'files', $files, $except);
        $this->encode_base();
    }

    // ofusca core
    function encode(){
        $ori = '/home/www/simplerest';
        $dst = "/tmp";  

        Files::delTree($dst);

        $str_except_files = <<<'FILES'
        app/core/ServiceProvider.php
        app/core/View.php
        app/core/Request.php
        app/core/Response.php
        app/core/Middleware.php
        app/core/controllers/Controller.php
        app/core/controllers/ConsoleController.php
        app/core/Acl.php
        app/core/interfaces
        app/core/exceptions
        app/core/helpers
        app/core/templates
        FILES;

        $exclude = explode(PHP_EOL, $str_except_files);
        Files::copy($ori, $dst, ['app/core'], $exclude);


        Files::delTree('./tmp');

        // llamar al ofuscador
        $ori = '/tmp/app/core';
        $dst = '/tmp/yakpro';
        $cmd = "php yakpro-po/yakpro-po.php $ori -o ./tmp";

        $ret = shell_exec($cmd);
        d($ret);

        // ahora copio los archivos ofuscados en el destino

        // seteo callback para remover comentarios
        Files::setCallback(function(string $content, string $path){
            return Strings::removeMultiLineComments($content);
        });

        $ori = '/home/www/simplerest/tmp/yakpro-po/obfuscated';
        $dst = "updates/{$this->last_update_dir}/";  
        Files::copy($ori, $dst . 'files/app/core');

        Files::setCallback(null);

        // Copio archivos excluidos sin ofuscar
        $ori = '/home/www/simplerest/';
        $dst = "updates/{$this->last_update_dir}/";  
        Files::copy($ori, $dst . 'files', $exclude);
    }

    function encode_base(){
        $ori = '/home/www/simplerest/app/';
        $dst = "/tmp/app/";  

        Files::delTree($dst);
        Files::mkDir($dst);

        /*
            En esta primera iteración solo ofuscaré unos pocos archivos
        */

        Files::copy($ori, $dst, [
            'core/libs/DB.php',
            'core/libs/Files.php',            
            'core/Model.php',
            'core/api/v1/ApiController.php',
            'core/libs/Strings.php',
            'core/libs/Reflector.php',
            'core/Container.php',
            'app/core/libs/System.php'
        ]);

        Files::delTree('./tmp');

        // llamar al ofuscador
        $ori = '/tmp/app/core';
        $dst = '/tmp/yakpro';
        $cmd = "php yakpro-po/yakpro-po.php $ori -o ./tmp";

        $ret = shell_exec($cmd);
        d($ret);
        
        // ahora copio los archivos ofuscados en el destino

        // seteo callback para remover comentarios
        Files::setCallback(function(string $content, string $path){
            return Strings::removeMultiLineComments($content);
        });

        $ori = '/home/www/simplerest/tmp/yakpro-po/obfuscated';
        $dst = "updates/{$this->last_update_dir}/";  
        Files::copy($ori, $dst . 'files/app/core'); // bug con glob:*
    }

    function zip(){
        Update::compress($this->last_update_dir);
    }

}

