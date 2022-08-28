<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\libs\DB;
use simplerest\core\libs\Url;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Date;

class Dumb2Controller extends MyController
{
  static function testx(){
    dd(
      (bool) ' '
    );
  }


  static function get_url_slugs(){
    $url = "http://127.0.0.1:8889/api/xxx/777/";

    dd(
      Url::getSlugs($url)
    );
  }
  
  static function get_rand_hex(){
    return Strings::randomHexaString(6);
  }


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


  function create_tb(){
    DB::getConnection('mpp');
  
    DB::statement("CREATE TABLE `TBL_TIPO_VINCULO_OER`;");

    dd(
      DB::getTableNames()
    ); 
  }

  function test_db(){
    DB::getConnection('mpp');
  
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

