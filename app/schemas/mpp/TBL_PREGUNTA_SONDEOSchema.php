<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PREGUNTA_SONDEOSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PREGUNTA_SONDEO',

			'id_name'		=> 'PRE_ID',

			'fields'		=> ['PRE_ID', 'PRE_TITULO', 'PRE_ENUNCIADO', 'PRE_BORRADO', 'SON_ID', 'PRE_MULTIPLE_RESPUESTA'],

			'attr_types'	=> [
				'PRE_ID' => 'INT',
				'PRE_TITULO' => 'STR',
				'PRE_ENUNCIADO' => 'STR',
				'PRE_BORRADO' => 'INT',
				'SON_ID' => 'INT',
				'PRE_MULTIPLE_RESPUESTA' => 'INT'
			],

			'primary'		=> ['PRE_ID'],

			'autoincrement' => 'PRE_ID',

			'nullable'		=> ['PRE_ID', 'PRE_BORRADO', 'PRE_MULTIPLE_RESPUESTA'],

			'required'		=> ['PRE_TITULO', 'PRE_ENUNCIADO', 'SON_ID'],

			'uniques'		=> [],

			'rules' 		=> [
				'PRE_ID' => ['type' => 'int'],
				'PRE_TITULO' => ['type' => 'str', 'max' => 200, 'required' => true],
				'PRE_ENUNCIADO' => ['type' => 'str', 'max' => 200, 'required' => true],
				'PRE_BORRADO' => ['type' => 'bool'],
				'SON_ID' => ['type' => 'int', 'required' => true],
				'PRE_MULTIPLE_RESPUESTA' => ['type' => 'bool']
			],

			'fks' 			=> ['SON_ID'],

			'relationships' => [
				'TBL_SONDEOS' => [
					['TBL_SONDEOS.SON_ID','TBL_PREGUNTA_SONDEO.SON_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_SONDEOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_SONDEOS',
        1 => 'SON_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PREGUNTA_SONDEO',
        1 => 'SON_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_SONDEOS' => [
					['TBL_SONDEOS.SON_ID','TBL_PREGUNTA_SONDEO.SON_ID']
				]
			],

			'expanded_relationships_from' => array (
  'TBL_SONDEOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_SONDEOS',
        1 => 'SON_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PREGUNTA_SONDEO',
        1 => 'SON_ID',
      ),
    ),
  ),
)
		];
	}	
}

