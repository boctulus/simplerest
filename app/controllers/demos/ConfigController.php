<?php

namespace simplerest\controllers\demos;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Config;
use simplerest\core\libs\Factory;
use simplerest\core\controllers\Controller;

class ConfigController extends Controller
{
    function test_config_get()
    {
        dd(Config::get('db_connection_default'));
        Config::set('db_connection_default', 'db_flor');
        dd(Config::get('db_connection_default'));

        DB::getDefaultConnection(); // -- ok
        dd(DB::select('SELECT * FROM tbl_usuario'));
    }
}

