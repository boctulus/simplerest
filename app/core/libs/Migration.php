<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Interfaces\IMigration;

class Migration extends IMigration
{
    protected $connection = null;
    protected $table      = null;

    function __construct(){
        if ($this->connection !== null){
            DB::setConnection($this->connection);
        }
    }
}

