<?php

namespace simplerest\controllers;

use simplerest\core\Controller;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\libs\Factory;
use simplerest\libs\DB;
use simplerest\libs\Schema;
use simplerest\libs\Strings;
use simplerest\core\MakeControllerBase;

class TestController extends Controller
{
    function __construct()
    {
        parent::__construct();        
    }

    function index(){
        return "Hello my friend";
    }

    function rels(){
        DB::getConnection('db_flor');

        $rels = Schema::getAllRelations('tbl_estado_civil', true);
        dd($rels);
    }

    function autojoins(){
        DB::getConnection('db_flor');

        $rows = DB::table('tbl_estado_civil')
        ->join('tbl_usuario')
        ->join('tbl_estado')
        ->get();

        dd($rows);
    }

    function x(){
        dd(assets("jota.jpg"));
    }

    function dir(){
        $path = '/home/www/simplerest/app/migrations';

        $dir  = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);

        echo "[$path]\n";
        foreach ($files as $file) {
            $indent = str_repeat('   ', $files->getDepth());
            echo $indent, " ├ $file\n";
        }
    }

    /*
        Genera migraciones a partir de la tabla 'tbl_scritp_tablas'
    */
    function gen_scripts(){
        $mk = new MakeControllerBase();

        $rows = DB::table('tbl_scritp_tablas')
        ->orderBy(['scr_intOrden' => 'ASC'])
        ->get();

        foreach ($rows as $row){
            $orden = str_pad($row['scr_intOrden'], 4, "0", STR_PAD_LEFT);
            $name  = strtolower("$orden-{$row['scr_varNombre']}-{$row['scr_varModulo']}");
            $script = $row['scr_lonScritp'];

            $folder = "compania";

            $class_name = Strings::snakeToCamel("{$row['scr_varNombre']}_{$row['scr_varModulo']}_{$row['scr_intOrden']}");

            $mk->migration("$name", "--dir=$folder", "--from_script=\"$script\"", "--class_name=$class_name");
        }
    }

    function mid(){
        return "Hello World!";        
    }

    function update(){
        $data = [ "est_varColor" => "rojo" ];

        DB::setConnection('db_flor');

        $affected = DB::table('tbl_estado')->where(["est_intId" => 1])->update($data);
        dd($affected);
    }

    function migrate(){
        $mgr = new MigrationsController();

        $folder = 'compania';
        $tenant = 'db_100';

        MigrationsController::hideResponse();

        $mgr->migrate("--dir=$folder", "--to=$tenant");
    }
    
    function mk(){
        $tenant = "db_100";
        
        MakeController::hideResponse();

        $mk = new MakeControllerBase();
		$mk->any("all", "-s", "-m", "--from:$tenant");        
    }

    
}

