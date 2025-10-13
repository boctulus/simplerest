<?php 

declare(strict_types=1);

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\ConsoleController;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\StdOut;
use Boctulus\Simplerest\Core\Libs\Update;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\Obfuscator;

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
        StdOut::print("Do you need help? You can copy | zip");
    }

    function copy(){
        $ori = '/home/www/simplerest';
        $dst = ROOT_PATH . "updates/{$this->last_update_dir}/";  
              
        /*
            Revisar porque no está excluyendo!
        */    
        $except =  [
            'initial_file_copy.batch',
            'PrepareUpdateController.php',
            'docs/dev',
            'glob:*.zip',
            'PASARELAS DE PAGO.txt',
            'yakpro-po',
            'vendor',
            'version.txt',
            '.git',
            '.gitignore',
            'updates',
            'public/assets',
            '*.zip',
            'tmp'
        ];

        Files::delTree($dst);

        Files::copy($ori, $dst . 'files', [ 'glob:*' ], $except);
        $this->encode();
    }

    // ofusca
    function encode(){
        $ori = ROOT_PATH . "updates/{$this->last_update_dir}/";
        $dst = ROOT_PATH . "/tmp/obfuscated";  

        $excluded = <<<'FILES'
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

        $ok = Obfuscator::obfuscate($ori, $dst, $excluded);

        Files::copy($dst, $ori);
    }


    function zip(){
        Update::compress($this->last_update_dir);
    }

}

