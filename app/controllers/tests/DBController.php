<?php

namespace simplerest\controllers\tests;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Schema;
use simplerest\core\libs\Factory;
use simplerest\controllers\MyController;

class DBController extends MyController
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
        config()['db_connection_default'] = 'db2';

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

