<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ContadorSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'contador',

			'id_name'		=> 'vch_conttabla',

			'attr_types'	=> [
				'vch_conttabla' => 'STR',
				'int_contitem' => 'INT',
				'int_contlongitud' => 'INT'
			],

			'primary'		=> ['vch_conttabla'],

			'autoincrement' => null,

			'nullable'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'vch_conttabla' => ['type' => 'str', 'max' => 30, 'required' => true],
				'int_contitem' => ['type' => 'int', 'required' => true],
				'int_contlongitud' => ['type' => 'int', 'required' => true]
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

