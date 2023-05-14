<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_OPCIONES_RESPUESTA_PROCESOSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_OPCIONES_RESPUESTA_PROCESO',

			'id_name'		=> 'ID_OPC',

			'fields'		=> ['ID_OPC', 'PRO_ID', 'OPC_LISTA_OPCIONES', 'OPC_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_OPC' => 'INT',
				'PRO_ID' => 'INT',
				'OPC_LISTA_OPCIONES' => 'STR',
				'OPC_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_OPC'],

			'autoincrement' => 'ID_OPC',

			'nullable'		=> ['ID_OPC', 'PRO_ID', 'OPC_LISTA_OPCIONES', 'OPC_BORRADO', 'created_at', 'updated_at'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'ID_OPC' => ['type' => 'int'],
				'PRO_ID' => ['type' => 'int'],
				'OPC_LISTA_OPCIONES' => ['type' => 'str'],
				'OPC_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 			=> [],

			'relationships' => [
				
			],

			'expanded_relationships' => array (
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}

