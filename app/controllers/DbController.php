<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\libs\DB;

/*
    Es mejor implementar routes para linea de comandos de una vez !
*/
class DbController extends MyController
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

