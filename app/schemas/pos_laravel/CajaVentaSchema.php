<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CajaVentaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'caja_venta',

			'id_name'			=> 'idCaja_venta',

			'fields'			=> ['idCaja_venta', 'idCarrito', 'idUsuario', 'idEmpresa', 'nombre', 'activo', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idCaja_venta' => 'INT',
				'idCarrito' => 'INT',
				'idUsuario' => 'INT',
				'idEmpresa' => 'INT',
				'nombre' => 'STR',
				'activo' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idCaja_venta'],

			'autoincrement' 	=> 'idCaja_venta',

			'nullable'			=> ['idCaja_venta', 'idCarrito', 'nombre', 'activo', 'created_at', 'updated_at'],

			'required'			=> ['idUsuario', 'idEmpresa'],

			'uniques'			=> [],

			'rules' 			=> [
				'idCaja_venta' => ['type' => 'int'],
				'idCarrito' => ['type' => 'int'],
				'idUsuario' => ['type' => 'int', 'required' => true],
				'idEmpresa' => ['type' => 'int', 'required' => true],
				'nombre' => ['type' => 'str', 'max' => 45],
				'activo' => ['type' => 'int'],
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

