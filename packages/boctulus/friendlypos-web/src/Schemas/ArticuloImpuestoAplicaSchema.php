<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ArticuloImpuestoAplicaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'articulo_impuesto_aplica',

			'id_name'			=> 'idArticulo_impuesto_aplicacion',

			'fields'			=> ['idArticulo_impuesto_aplicacion', 'nombre'],

			'attr_types'		=> [
				'idArticulo_impuesto_aplicacion' => 'INT',
				'nombre' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idArticulo_impuesto_aplicacion'],

			'autoincrement' 	=> 'idArticulo_impuesto_aplicacion',

			'nullable'			=> ['idArticulo_impuesto_aplicacion', 'nombre'],

			'required'			=> [],

			'uniques'			=> [],

			'rules' 			=> [
				'idArticulo_impuesto_aplicacion' => ['type' => 'int'],
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

