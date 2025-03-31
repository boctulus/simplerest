<?php

namespace Boctulus\Simplerest\Controllers\demos;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;

class DBController extends Controller
{
    function test_get_db_ver(){
        DB::getDefaultConnection();

        dd(DB::driver(), 'Driver');
        dd(DB::driverVersion(), 'Driver version');
        dd(DB::driverVersion(true), 'Driver version (num)');
        dd(DB::isMariaDB(), 'Is MariaDB');
    }

    function test_conn()
    {
        DB::setConnection('parts');
        
        dd(
            Schema::getTables()
        , 'TABLES');
    }

    function test_conn2()
    {
        Config::get()['db_connection_default'] = 'db2';

        $sc = new Schema('cables');

        $sc
            ->int('id')->unsigned()->auto()->pri()
            ->varchar('nombre', 40)
            ->float('calibre')

            ->create();
    }

    function statement(){
        DB::getConnection('mpp');
      
        DB::statement("CREATE TABLE `TBL_TIPO_VINCULO_OER`;");
    
        dd(
          DB::getTableNames()
        ); 
    }


}

