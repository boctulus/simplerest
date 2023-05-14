<?php

namespace simplerest\schemas\parts;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblProductosSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_productos',

			'id_name'		=> 'prod_id',

			'fields'		=> ['prod_id', 'id_prov', 'descripcion_producto', 'prod_precio', 'prod_stock', 'empresa'],

			'attr_types'	=> [
				'prod_id' => 'STR',
				'id_prov' => 'STR',
				'descripcion_producto' => 'STR',
				'prod_precio' => 'INT',
				'prod_stock' => 'INT',
				'empresa' => 'STR'
			],

			'primary'		=> ['prod_id'],

			'autoincrement' => null,

			'nullable'		=> [],

			'required'		=> ['prod_id', 'id_prov', 'descripcion_producto', 'prod_precio', 'prod_stock', 'empresa'],

			'uniques'		=> [],

			'rules' 		=> [
				'prod_id' => ['type' => 'str', 'max' => 7, 'required' => true],
				'id_prov' => ['type' => 'str', 'max' => 50, 'required' => true],
				'descripcion_producto' => ['type' => 'str', 'max' => 255, 'required' => true],
				'prod_precio' => ['type' => 'int', 'required' => true],
				'prod_stock' => ['type' => 'int', 'required' => true],
				'empresa' => ['type' => 'str', 'max' => 50, 'required' => true]
			],

			'fks' 			=> [],

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

