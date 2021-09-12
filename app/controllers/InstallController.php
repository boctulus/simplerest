<?php

namespace simplerest\controllers;

use simplerest\core\ConsoleController;
use simplerest\core\Model;

class InstallController extends ConsoleController
{
    function __construct()
    {
        parent::__construct();
        $this->install();
    }

    private function install(){
        $file = file_get_contents(ETC_PATH . 'db_base.sql');
        
        /*
            Estoy seguro no hay un ; más que para indicar terminación de sentencias
        */
        $sentences = explode(';', $file);
        
        foreach ($sentences as $sentence){
            $sentence = trim($sentence);

            if ($sentence == ''){
                continue;
            }

            dd($sentence, 'SENTENCE');

            try {
                $ok = Model::query($sentence);
            } catch (\Exception $e){
                dd($e, 'Sql Exception');
            }
        }
    }

}

