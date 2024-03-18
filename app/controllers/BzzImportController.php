<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;

class BzzImportController extends MyController
{
    // Simulo proceso de importacion
    function do_process()
    {
        // Mock de registros de productos
        $rows = [
            '{A}','{B}','{C}','{D}','{E}','{F}'
        ];

        $cnt  = count($rows);
   
        foreach ($rows as $cur => $row){
            // some heavy work
            // sleep(2);
            set_transient('bzz-import_completion', round($cur / $cnt) * 100, 9999);

            dd(get_transient('bzz-import_completion', 0));
        }        
    }

    function get_completion()
    {
       $data = [
        'completion' => get_transient('bzz-import_completion', 0)
       ];

       response()->send($data);
    }

    function index()
    {
        dd("Index of ". __CLASS__);                   
    }
}

