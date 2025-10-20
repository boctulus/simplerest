<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class TicketVentaDetalleItemExtraSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'ticket_venta_detalle_item_extra',

			'id_name'			=> null,

			'fields'			=> ['idTicket_venta_detalle_item_extra', 'idTicket_venta', 'idProducto', 'cantidad', 'precio', 'descuento', 'nombre', 'descripcion', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idTicket_venta_detalle_item_extra' => 'INT',
				'idTicket_venta' => 'INT',
				'idProducto' => 'INT',
				'cantidad' => 'INT',
				'precio' => 'INT',
				'descuento' => 'INT',
				'nombre' => 'STR',
				'descripcion' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> [],

			'autoincrement' 	=> null,

			'nullable'			=> ['idTicket_venta', 'idProducto', 'created_at', 'updated_at'],

			'required'			=> ['idTicket_venta_detalle_item_extra', 'cantidad', 'precio', 'descuento', 'nombre', 'descripcion'],

			'uniques'			=> [],

			'rules' 			=> [
				'idTicket_venta_detalle_item_extra' => ['type' => 'int', 'required' => true],
				'idTicket_venta' => ['type' => 'int'],
				'idProducto' => ['type' => 'int'],
				'cantidad' => ['type' => 'int', 'required' => true],
				'precio' => ['type' => 'int', 'required' => true],
				'descuento' => ['type' => 'int', 'required' => true],
				'nombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'descripcion' => ['type' => 'str', 'max' => 100, 'required' => true],
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

