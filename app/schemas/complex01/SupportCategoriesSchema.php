<?php

namespace simplerest\schemas\complex01;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class SupportCategoriesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'support_categories',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name'],

			'attr_types'		=> [
				'id' => 'INT',
				'name' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id'],

			'required'			=> ['name'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 100, 'required' => true]
			],

			'fks' 				=> [],

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

