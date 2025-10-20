<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class TipoProductoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'tipo_producto',

			'id_name'			=> 'idTipo_producto',

			'fields'			=> ['idTipo_producto', 'nombre'],

			'attr_types'		=> [
				'idTipo_producto' => 'INT',
				'nombre' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idTipo_producto'],

			'autoincrement' 	=> 'idTipo_producto',

			'nullable'			=> ['idTipo_producto', 'nombre'],

			'required'			=> [],

			'uniques'			=> [],

			'rules' 			=> [
				'idTipo_producto' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 45]
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

