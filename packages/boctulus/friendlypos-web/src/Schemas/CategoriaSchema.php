<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CategoriaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'categoria',

			'id_name'			=> 'idCategoria',

			'fields'			=> ['idCategoria', 'idEmpresa', 'nombre', 'activo', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idCategoria' => 'INT',
				'idEmpresa' => 'INT',
				'nombre' => 'STR',
				'activo' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idCategoria'],

			'autoincrement' 	=> 'idCategoria',

			'nullable'			=> ['idCategoria', 'nombre', 'activo', 'created_at', 'updated_at'],

			'required'			=> ['idEmpresa'],

			'uniques'			=> [],

			'rules' 			=> [
				'idCategoria' => ['type' => 'int'],
				'idEmpresa' => ['type' => 'int', 'required' => true],
				'nombre' => ['type' => 'str', 'max' => 45],
				'activo' => ['type' => 'int'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> [],

			'relationships' => [
				'articulo' => [
					['articulo.idCategoria','categoria.idCategoria']
				]
			],

			'expanded_relationships' => array (
  'articulo' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'articulo',
        1 => 'idCategoria',
      ),
      1 => 
      array (
        0 => 'categoria',
        1 => 'idCategoria',
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

