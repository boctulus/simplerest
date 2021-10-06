<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ProductCommentsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'product_comments',

			'id_name'		=> 'product_id',

			'attr_types'	=> [
				'id' => 'INT',
				'text' => 'STR',
				'product_id' => 'INT'
			],

			'nullable'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'required' => true],
				'text' => ['type' => 'str', 'max' => 144, 'required' => true],
				'product_id' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'products' => [
					['products.id','product_comments.product_id']
				]
			]
		];
	}	
}

