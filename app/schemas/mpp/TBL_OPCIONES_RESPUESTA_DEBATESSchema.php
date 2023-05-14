<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_OPCIONES_RESPUESTA_DEBATESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_OPCIONES_RESPUESTA_DEBATES',

			'id_name'		=> 'ID_OPC',

			'fields'		=> ['ID_OPC', 'DEB_ID', 'OPC_LISTA_OPCIONES', 'OPC_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_OPC' => 'INT',
				'DEB_ID' => 'INT',
				'OPC_LISTA_OPCIONES' => 'STR',
				'OPC_BORRADO' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_OPC'],

			'autoincrement' => 'ID_OPC',

			'nullable'		=> ['ID_OPC', 'DEB_ID', 'OPC_LISTA_OPCIONES', 'OPC_BORRADO', 'created_at', 'updated_at'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'ID_OPC' => ['type' => 'int'],
				'DEB_ID' => ['type' => 'int'],
				'OPC_LISTA_OPCIONES' => ['type' => 'str'],
				'OPC_BORRADO' => ['type' => 'str', 'max' => 45],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 			=> ['DEB_ID'],

			'relationships' => [
				'TBL_DEBATES' => [
					['TBL_DEBATES.DEB_ID','TBL_OPCIONES_RESPUESTA_DEBATES.DEB_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_DEBATES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_DEBATES',
        1 => 'DEB_ID',
      ),
      1 => 
      array (
        0 => 'TBL_OPCIONES_RESPUESTA_DEBATES',
        1 => 'DEB_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_DEBATES' => [
					['TBL_DEBATES.DEB_ID','TBL_OPCIONES_RESPUESTA_DEBATES.DEB_ID']
				]
			],

			'expanded_relationships_from' => array (
  'TBL_DEBATES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_DEBATES',
        1 => 'DEB_ID',
      ),
      1 => 
      array (
        0 => 'TBL_OPCIONES_RESPUESTA_DEBATES',
        1 => 'DEB_ID',
      ),
    ),
  ),
)
		];
	}	
}

