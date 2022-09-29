<?php

namespace simplerest\controllers;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\core\libs\Url;
use simplerest\core\libs\Date;
use simplerest\core\libs\Files;
use simplerest\core\libs\Strings;
use simplerest\controllers\MyController;

class Dumb2Controller extends MyController
{
  function testy(){
    dd(
      Strings::parseCurrency('son EUR 108.000,40 a pagar', '.', ',')
    );
  }

  function testx(){
    DB::getConnection();

    dd(
      DB::driver()
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

  function xrz(){
    $files = Files::glob('D:\\www\\pruebas\\jsons', '*.json');

    $arr = [];

    foreach ($files as $file){
        $json = trim(file_get_contents($file));
        $new  = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json), true );

        if ($new === null){
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    echo ' - No errors';
                break;
                case JSON_ERROR_DEPTH:
                    echo ' - Maximum stack depth exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    echo ' - Underflow or the modes mismatch';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    echo ' - Unexpected control character found';
                break;
                case JSON_ERROR_SYNTAX:
                    echo ' - Syntax error, malformed JSON';
                break;
                case JSON_ERROR_UTF8:
                    echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
                default:
                    echo ' - Unknown error';
                break;
            }
            exit;
        };

        $arr = array_merge($arr, $new['prods']);
    }

    
    Files::varExport('D:\www\pruebas\jsons\prods.php', $arr);
}

}

