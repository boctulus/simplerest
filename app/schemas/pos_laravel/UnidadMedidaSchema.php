<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class UnidadMedidaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'unidad_medida',

			'id_name'			=> 'idUnidad',

			'fields'			=> ['idUnidad', 'nombre'],

			'attr_types'		=> [
				'idUnidad' => 'INT',
				'nombre' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idUnidad'],

			'autoincrement' 	=> 'idUnidad',

			'nullable'			=> ['idUnidad', 'nombre'],

			'required'			=> [],

			'uniques'			=> [],

			'rules' 			=> [
				'idUnidad' => ['type' => 'int'],
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

