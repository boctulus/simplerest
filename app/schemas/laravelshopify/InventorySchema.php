<?php

namespace simplerest\schemas\laravelshopify;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class InventorySchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'inventory',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'product_id', 'quantity', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'product_id' => 'INT',
				'quantity' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'quantity', 'created_at', 'updated_at'],

			'required'			=> ['product_id'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int', 'min' => 0],
				'product_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'quantity' => ['type' => 'int'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['product_id'],

			'relationships' => [
				'products' => [
					['products.id','inventory.product_id']
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
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'inventory',
        1 => 'product_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'products' => [
					['products.id','inventory.product_id']
				]
			],

			'expanded_relationships_from' => array (
  'products' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'products',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'inventory',
        1 => 'product_id',
      ),
    ),
  ),
)
		];
	}	
}

