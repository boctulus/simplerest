<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Interfaces\IMigration;

class Migration implements IMigration
{
    protected $connection = null;
    protected $table      = null;

    function __construct(){
        if ($this->connection !== null){
            DB::setConnection($this->connection);
        }
    }

    function up(){
        ### UP
    }

    function down() {
        ### DOWN
    }
}

