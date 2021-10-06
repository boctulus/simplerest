<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductTagsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'product_tags',

			'id_name'		=> 'id_tag',

			'attr_types'	=> [
				'id_tag' => 'INT',
				'name' => 'STR',
				'comment' => 'STR',
				'product_id' => 'INT'
			],

			'nullable'		=> ['id_tag', 'comment'],

			'rules' 		=> [
				'id_tag' => ['type' => 'int'],
				'name' => ['type' => 'str', 'max' => 30, 'required' => true],
				'comment' => ['type' => 'str', 'max' => 60],
				'product_id' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'products' => [
					['products.id','product_tags.product_id']
				]
			]
		];
	}	
}

