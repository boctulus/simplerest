<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Date;

class Dumb2Controller extends MyController
{
  function rand_time(){
    $fecha = Date::subDays(Date::date(), rand(0, 365)) . ' '. Date::randomTime(true);
    dd($fecha, 'DATETIME');
  }

  function test_remove_sp()
  {
      $str = "		array (
          'ID_COC' => 
          array (
            'type' => 'int',
            'min' => 0,
          ),
          'COC_NOMBRE' => 
          array (
            'type' => 'str',
            'max' => 60,
            'required' => true,
          ),
          'COC_BORRADO' => 
          array (
            'type' => 'bool',
          ),
          'created_at' => 
          array (
            'type' => 'timestamp',
          ),
          'updated_at' => 
          array (
            'type' => 'timestamp',
          ),
        );";
        
        echo Strings::trimMultiline($str). PHP_EOL;
  }
}

