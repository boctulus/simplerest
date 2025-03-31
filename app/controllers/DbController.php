<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\DB;

/*
    Es mejor implementar routes para linea de comandos de una vez !
*/
class DbController extends Controller
{
    protected $db_collation = "utf8mb4_general_ci";
    protected $charset      = 'utf8mb4';

    function create($db_name)
    {
        $sql = "CREATE DATABASE  `$db_name` CHARACTER SET {$this->charset} COLLATE {$this->db_collation}";
        DB::statement($sql);
        dd($sql, 'SQL CMD --done');
    }
}

