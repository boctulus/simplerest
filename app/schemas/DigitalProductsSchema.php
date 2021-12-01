<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class DigitalProductsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'digital_products',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'downloable' => 'INT',
				'restricted' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'downloable', 'restricted'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'downloable' => ['type' => 'bool'],
				'restricted' => ['type' => 'bool']
			],

			'fks' 			=> [],

			'relationships' => [
				'products' => [
					['products.digital_id','digital_products.id']
				]
			],

			'expanded_relationships' => array (
				  'products' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'products',
				        1 => 'digital_id',
				      ),
				      1 => 
				      array (
				        0 => 'digital_products',
				        1 => 'id',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
				)
		];
	}	
}

