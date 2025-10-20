<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CierreCajaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'cierre_caja',

			'id_name'			=> 'idCierre_caja',

			'fields'			=> ['idCierre_caja', 'idCaja_venta', 'idCarrito', 'idUsuario', 'idEmpresa', 'monto_inicial', 'fecha_inicio', 'monto_final', 'fecha_fin'],

			'attr_types'		=> [
				'idCierre_caja' => 'INT',
				'idCaja_venta' => 'INT',
				'idCarrito' => 'INT',
				'idUsuario' => 'INT',
				'idEmpresa' => 'INT',
				'monto_inicial' => 'INT',
				'fecha_inicio' => 'STR',
				'monto_final' => 'INT',
				'fecha_fin' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idCierre_caja', 'idCarrito', 'idUsuario', 'idEmpresa'],

			'autoincrement' 	=> 'idCierre_caja',

			'nullable'			=> ['idCierre_caja', 'monto_inicial', 'fecha_inicio', 'monto_final', 'fecha_fin'],

			'required'			=> ['idCaja_venta', 'idCarrito', 'idUsuario', 'idEmpresa'],

			'uniques'			=> [],

			'rules' 			=> [
				'idCierre_caja' => ['type' => 'int'],
				'idCaja_venta' => ['type' => 'int', 'required' => true],
				'idCarrito' => ['type' => 'int', 'required' => true],
				'idUsuario' => ['type' => 'int', 'required' => true],
				'idEmpresa' => ['type' => 'int', 'required' => true],
				'monto_inicial' => ['type' => 'int'],
				'fecha_inicio' => ['type' => 'datetime'],
				'monto_final' => ['type' => 'int'],
				'fecha_fin' => ['type' => 'datetime']
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

