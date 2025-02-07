<?php

namespace simplerest\schemas\laravelshopify;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class OrderStatusSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'order_status',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'name', 'description', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'name' => 'STR',
				'description' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'description', 'created_at', 'updated_at'],

			'required'			=> ['name'],

			'uniques'			=> ['name'],

			'rules' 			=> [
				'id' => ['type' => 'int', 'min' => 0],
				'name' => ['type' => 'str', 'max' => 255, 'required' => true],
				'description' => ['type' => 'str', 'max' => 255],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> [],

			'relationships' => [
				'orders' => [
					['orders.order_status_id','order_status.id']
				]
			],

			'expanded_relationships' => array (
  'orders' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'orders',
        1 => 'order_status_id',
      ),
      1 => 
      array (
        0 => 'order_status',
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

