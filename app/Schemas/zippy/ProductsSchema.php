<?php

namespace Boctulus\Simplerest\Schemas\zippy;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ProductsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'products',

			'id_name'			=> 'ean',

			'fields'			=> ['ean', 'description', 'net_content', 'unit_of_measurement', 'brand', 'img', 'categories', 'catego_raw1', 'catego_raw2', 'catego_raw3', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'ean' => 'INT',
				'description' => 'STR',
				'net_content' => 'STR',
				'unit_of_measurement' => 'STR',
				'brand' => 'STR',
				'img' => 'STR',
				'categories' => 'STR',
				'catego_raw1' => 'STR',
				'catego_raw2' => 'STR',
				'catego_raw3' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [
				'categories' => 'JSON'
			],

			'primary'			=> ['ean'],

			'autoincrement' 	=> null,

			'nullable'			=> ['description', 'net_content', 'unit_of_measurement', 'brand', 'img', 'categories', 'catego_raw1', 'catego_raw2', 'catego_raw3', 'updated_at'],

			'required'			=> ['ean', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'ean' => ['type' => 'int', 'min' => 0, 'required' => true],
				'description' => ['type' => 'str'],
				'net_content' => ['type' => 'decimal(10,2)'],
				'unit_of_measurement' => ['type' => 'str', 'max' => 50],
				'brand' => ['type' => 'str', 'max' => 100],
				'img' => ['type' => 'str', 'max' => 255],
				'categories' => ['type' => 'str'],
				'catego_raw1' => ['type' => 'str', 'max' => 100],
				'catego_raw2' => ['type' => 'str', 'max' => 100],
				'catego_raw3' => ['type' => 'str', 'max' => 100],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime']
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

