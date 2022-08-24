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
  function test_scopes(){
    DB::getConnection('az');  
    
    dd(
      DB::table('products')
      ->where(['id', 200, '>'])
      ->count(),
      'NORMAL'
    );

    dd(
      DB::table('products')
      ->where(['id', 200, '>'])
      ->costScope()
      ->count(),
      'SCOPE costScope'
    );
  }


  function create_db(){
    DB::getConnection('mpp');
  
    DB::statement("CREATE DATABASE `organizaciones`;");

    // dd(
    //   DB::getTableNames()
    // ); 
  }

  function test(){
    DB::getConnection('mpo_remote');
  
    dd(
      DB::getTableNames()
    );    
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

