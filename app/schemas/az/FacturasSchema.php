<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FacturasSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'facturas',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'created_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'created_at' => ['type' => 'datetime', 'required' => true]
			],

			'fks' 			=> [],

			'relationships' => [
				'factura_detalle' => [
					['factura_detalle.factura_id','facturas.id']
				]
			],

			'expanded_relationships' => array (
				  'factura_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'factura_detalle',
				        1 => 'factura_id',
				      ),
				      1 => 
				      array (
				        0 => 'facturas',
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

