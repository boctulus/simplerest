<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductCategoriesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'product_categories',

			'id_name'		=> 'id_catego',

			'attr_types'	=> [
				'id_catego' => 'INT',
				'name_catego' => 'STR'
			],

			'nullable'		=> ['id_catego'],

			'rules' 		=> [
				'id_catego' => ['type' => 'int'],
				'name_catego' => ['type' => 'str', 'max' => 80, 'required' => true]
			],

			'relationships' => [
				
			]
		];
	}	
}

