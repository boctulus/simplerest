<?php

namespace Boctulus\Simplerest\Controllers\demos;

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Request;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Controllers\Controller;

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

