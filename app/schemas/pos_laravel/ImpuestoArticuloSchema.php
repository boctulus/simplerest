<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ImpuestoArticuloSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'impuesto_articulo',

			'id_name'			=> 'idImpuesto_articulo',

			'fields'			=> ['idImpuesto_articulo', 'idProducto', 'idArticuloImpuesto'],

			'attr_types'		=> [
				'idImpuesto_articulo' => 'INT',
				'idProducto' => 'INT',
				'idArticuloImpuesto' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idImpuesto_articulo'],

			'autoincrement' 	=> 'idImpuesto_articulo',

			'nullable'			=> ['idImpuesto_articulo', 'idProducto', 'idArticuloImpuesto'],

			'required'			=> [],

			'uniques'			=> [],

			'rules' 			=> [
				'idImpuesto_articulo' => ['type' => 'int'],
				'idProducto' => ['type' => 'int'],
				'idArticuloImpuesto' => ['type' => 'int']
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

