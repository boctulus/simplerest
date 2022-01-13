<?php

namespace simplerest\controllers;

use simplerest\core\ConsoleController;
use simplerest\core\Model;
use simplerest\core\libs\Schema;
use simplerest\core\libs\DB;

class InstallController extends ConsoleController
{
    function __construct()
    {
        parent::__construct();
        $this->install();
    }

    private function install()
    {    
        Schema::disableForeignKeyConstraints();
        
        $res = shell_exec('php com migrations migrate');
        dd($res);
        
        Schema::enableForeignKeyConstraints();


        $res = shell_exec("php com make schema all -f --from:main");
        dd($res);

        $res = shell_exec("php com make model all --from:main");
        dd($res);
    }

}

