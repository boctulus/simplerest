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
				'name_catego' => ['max' => 80]
			],

			'relationships' => [
				
			]
		];
	}	
}

