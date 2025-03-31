<?php

namespace Boctulus\Simplerest\Schemas\complex01;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CustomerDetailsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'customer_details',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'customer_id', 'address', 'phone'],

			'attr_types'		=> [
				'id' => 'INT',
				'customer_id' => 'INT',
				'address' => 'STR',
				'phone' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'address', 'phone'],

			'required'			=> ['customer_id'],

			'uniques'			=> ['customer_id'],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'customer_id' => ['type' => 'int', 'required' => true],
				'address' => ['type' => 'str', 'max' => 255],
				'phone' => ['type' => 'str', 'max' => 20]
			],

			'fks' 				=> ['customer_id'],

			'relationships' => [
				'customers' => [
					['customers.id','customer_details.customer_id']
				]
			],

			'expanded_relationships' => array (
  'customers' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'customers',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'customer_details',
        1 => 'customer_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'customers' => [
					['customers.id','customer_details.customer_id']
				]
			],

			'expanded_relationships_from' => array (
  'customers' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'customers',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'customer_details',
        1 => 'customer_id',
      ),
    ),
  ),
)
		];
	}	
}

