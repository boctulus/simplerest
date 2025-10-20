<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ArticuloImpuestoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'articulo_impuesto',

			'id_name'			=> 'idArticulo_impuesto',

			'fields'			=> ['idArticulo_impuesto', 'IdEmpresa', 'TipoImpuesto', 'idArticulo_impuesto_aplicacion', 'nombre', 'porcentaje', 'activo', 'aplica_todos', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idArticulo_impuesto' => 'INT',
				'IdEmpresa' => 'INT',
				'TipoImpuesto' => 'STR',
				'idArticulo_impuesto_aplicacion' => 'INT',
				'nombre' => 'STR',
				'porcentaje' => 'STR',
				'activo' => 'INT',
				'aplica_todos' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idArticulo_impuesto'],

			'autoincrement' 	=> 'idArticulo_impuesto',

			'nullable'			=> ['idArticulo_impuesto', 'IdEmpresa', 'TipoImpuesto', 'idArticulo_impuesto_aplicacion', 'nombre', 'porcentaje', 'created_at', 'updated_at'],

			'required'			=> ['activo', 'aplica_todos'],

			'uniques'			=> [],

			'rules' 			=> [
				'idArticulo_impuesto' => ['type' => 'int'],
				'IdEmpresa' => ['type' => 'int'],
				'TipoImpuesto' => ['type' => 'str', 'max' => 20],
				'idArticulo_impuesto_aplicacion' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 45],
				'porcentaje' => ['type' => 'decimal(10,4)'],
				'activo' => ['type' => 'int', 'required' => true],
				'aplica_todos' => ['type' => 'int', 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
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

