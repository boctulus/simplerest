<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblBbbSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_bbb',

			'id_name'		=> 'ID',

			'fields'		=> ['ID', 'NOMBRE'],

			'attr_types'	=> [
				'ID' => 'INT',
				'NOMBRE' => 'STR'
			],

			'primary'		=> ['ID'],

			'autoincrement' => 'ID',

			'nullable'		=> ['ID'],

			'required'		=> ['NOMBRE'],

			'uniques'		=> [],

			'rules' 		=> [
				'ID' => ['type' => 'int'],
				'NOMBRE' => ['type' => 'str', 'max' => 60, 'required' => true]
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

