<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_COBERTURAS_TERRITORIALESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_COBERTURAS_TERRITORIALES',

			'id_name'		=> 'ID_CTR',

			'fields'		=> ['ID_CTR', 'CTR_TERRITORIOS', 'CTR_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_CTR' => 'INT',
				'CTR_TERRITORIOS' => 'STR',
				'CTR_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_CTR'],

			'autoincrement' => 'ID_CTR',

			'nullable'		=> ['ID_CTR', 'CTR_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['CTR_TERRITORIOS'],

			'uniques'		=> [],

			'rules' 		=> [
				'ID_CTR' => ['type' => 'int', 'min' => 0],
				'CTR_TERRITORIOS' => ['type' => 'str', 'required' => true],
				'CTR_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
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

