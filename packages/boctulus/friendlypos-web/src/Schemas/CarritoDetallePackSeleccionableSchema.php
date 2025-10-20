<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CarritoDetallePackSeleccionableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'carrito_detalle_pack_seleccionable',

			'id_name'			=> 'idcarrito_detalle_pack_seleccionable',

			'fields'			=> ['idcarrito_detalle_pack_seleccionable', 'idPack', 'idEmpresa_producto_pack', 'idCarrito', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idcarrito_detalle_pack_seleccionable' => 'INT',
				'idPack' => 'INT',
				'idEmpresa_producto_pack' => 'INT',
				'idCarrito' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idcarrito_detalle_pack_seleccionable'],

			'autoincrement' 	=> 'idcarrito_detalle_pack_seleccionable',

			'nullable'			=> ['idcarrito_detalle_pack_seleccionable', 'idEmpresa_producto_pack', 'idCarrito', 'created_at', 'updated_at'],

			'required'			=> ['idPack'],

			'uniques'			=> [],

			'rules' 			=> [
				'idcarrito_detalle_pack_seleccionable' => ['type' => 'int'],
				'idPack' => ['type' => 'int', 'required' => true],
				'idEmpresa_producto_pack' => ['type' => 'int'],
				'idCarrito' => ['type' => 'int'],
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

