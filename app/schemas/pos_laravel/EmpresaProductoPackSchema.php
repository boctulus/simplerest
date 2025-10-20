<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class EmpresaProductoPackSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'empresa_producto_pack',

			'id_name'			=> 'idPack',

			'fields'			=> ['idPack', 'idEmpresa_producto', 'idProducto', 'nombre', 'activo', 'cantidad', 'seleccionable', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idPack' => 'INT',
				'idEmpresa_producto' => 'INT',
				'idProducto' => 'INT',
				'nombre' => 'STR',
				'activo' => 'INT',
				'cantidad' => 'INT',
				'seleccionable' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idPack'],

			'autoincrement' 	=> 'idPack',

			'nullable'			=> ['idPack', 'idProducto', 'nombre', 'activo', 'cantidad', 'seleccionable', 'created_at', 'updated_at'],

			'required'			=> ['idEmpresa_producto'],

			'uniques'			=> [],

			'rules' 			=> [
				'idPack' => ['type' => 'int'],
				'idEmpresa_producto' => ['type' => 'int', 'required' => true],
				'idProducto' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 45],
				'activo' => ['type' => 'int'],
				'cantidad' => ['type' => 'int'],
				'seleccionable' => ['type' => 'int'],
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

