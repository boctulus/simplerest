<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Logger;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\VarDump;
use Boctulus\Simplerest\Core\Controllers\Controller;

class BzzImportController extends Controller
{
    function run(){
        // Resuelvo CORS
        cors();

        // Ejecuto el proceso en segundo plano
        $pid = bg_com("bzz_import do_process");
    }   

    // Simulo proceso de importacion
    function do_process()
    {
        // Mock de registros de productos
        $rows = [
            '{A}','{B}','{C}','{D}','{E}','{F}','{G}','{H}'
        ];

        set_transient('bzz-importer_completion', 0, 9999);
        Logger::dd(get_transient('bzz-importer_completion', 0), 'COMPLETION %');

        $cnt = count($rows);
   
        try {
            foreach ($rows as $cur => $row){
                Logger::dd("Processing row='$cur' ({$row})");

                // some heavy work
                sleep(2);
                set_transient('bzz-importer_completion', round(($cur+1) * 100 / $cnt), 9999);
                // Logger::dd(get_transient('bzz-importer_completion', 0), 'COMPLETION %');
            }     

            set_transient('bzz-importer_completion', 100, 9999);   
            // Logger::dd(get_transient('bzz-importer_completion', 0), 'COMPLETION %');
        } catch (\Exception $e){
            Logger::logError($e->getMessage());
        }
    }

    function get_completion()
    {
       $data = [
        'completion' => get_transient('bzz-importer_completion', 0)
       ];

       response()->send($data);
    }

    function index()
    {
        dd("Index of ". __CLASS__);                   
    }
}

