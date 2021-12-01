<?php

namespace simplerest\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ValoracionesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'valoraciones',

			'id_name'		=> 'id_val',

			'attr_types'	=> [
				'id_val' => 'INT',
				'texto' => 'STR',
				'otro_campo' => 'INT'
			],

			'primary'		=> ['id_val'],

			'autoincrement' => 'id_val',

			'nullable'		=> ['id_val', 'otro_campo'],

			'uniques'		=> [],

			'rules' 		=> [
				'id_val' => ['type' => 'int'],
				'texto' => ['type' => 'str', 'max' => 20, 'required' => true],
				'otro_campo' => ['type' => 'int']
			],

			'fks' 			=> [],

			'relationships' => [
				'product_valoraciones' => [
					['product_valoraciones.valoracion_id','valoraciones.id_val']
				]
			],

			'expanded_relationships' => array (
				  'product_valoraciones' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'product_valoraciones',
				        1 => 'valoracion_id',
				      ),
				      1 => 
				      array (
				        0 => 'valoraciones',
				        1 => 'id_val',
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

