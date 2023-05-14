<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_OPCIONES_RESPUESTA_SONDEOSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_OPCIONES_RESPUESTA_SONDEO',

			'id_name'		=> 'ID_OPC',

			'fields'		=> ['ID_OPC', 'SON_ID', 'OPC_LISTA_OPCIONES', 'OPC_BORRADO', 'CIU_ID'],

			'attr_types'	=> [
				'ID_OPC' => 'INT',
				'SON_ID' => 'INT',
				'OPC_LISTA_OPCIONES' => 'STR',
				'OPC_BORRADO' => 'INT',
				'CIU_ID' => 'INT'
			],

			'primary'		=> ['ID_OPC'],

			'autoincrement' => 'ID_OPC',

			'nullable'		=> ['ID_OPC', 'OPC_LISTA_OPCIONES', 'OPC_BORRADO'],

			'required'		=> ['SON_ID', 'CIU_ID'],

			'uniques'		=> [],

			'rules' 		=> [
				'ID_OPC' => ['type' => 'int'],
				'SON_ID' => ['type' => 'int', 'required' => true],
				'OPC_LISTA_OPCIONES' => ['type' => 'str'],
				'OPC_BORRADO' => ['type' => 'bool'],
				'CIU_ID' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['SON_ID'],

			'relationships' => [
				'TBL_SONDEOS' => [
					['TBL_SONDEOS.SON_ID','TBL_OPCIONES_RESPUESTA_SONDEO.SON_ID']
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
        0 => 'TBL_OPCIONES_RESPUESTA_SONDEO',
        1 => 'SON_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_SONDEOS' => [
					['TBL_SONDEOS.SON_ID','TBL_OPCIONES_RESPUESTA_SONDEO.SON_ID']
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
        0 => 'TBL_OPCIONES_RESPUESTA_SONDEO',
        1 => 'SON_ID',
      ),
    ),
  ),
)
		];
	}	
}

