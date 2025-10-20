<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class TicketVentaDetallePackSeleccionableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'ticket_venta_detalle_pack_seleccionable',

			'id_name'			=> null,

			'fields'			=> ['idTicket_venta_detalle_pack_seleccionable', 'idTicket_venta', 'idPack', 'idEmpresa_producto_pack', 'idCarrito', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idTicket_venta_detalle_pack_seleccionable' => 'INT',
				'idTicket_venta' => 'INT',
				'idPack' => 'INT',
				'idEmpresa_producto_pack' => 'INT',
				'idCarrito' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> [],

			'autoincrement' 	=> null,

			'nullable'			=> ['idTicket_venta', 'idPack', 'idEmpresa_producto_pack', 'idCarrito', 'created_at', 'updated_at'],

			'required'			=> ['idTicket_venta_detalle_pack_seleccionable'],

			'uniques'			=> [],

			'rules' 			=> [
				'idTicket_venta_detalle_pack_seleccionable' => ['type' => 'int', 'required' => true],
				'idTicket_venta' => ['type' => 'int'],
				'idPack' => ['type' => 'int'],
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

