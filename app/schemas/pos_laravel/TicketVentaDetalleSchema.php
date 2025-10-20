<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class TicketVentaDetalleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'ticket_venta_detalle',

			'id_name'			=> null,

			'fields'			=> ['idTicket_venta_detalle', 'idTicket_venta', 'idProducto', 'cantidad', 'descuento'],

			'attr_types'		=> [
				'idTicket_venta_detalle' => 'INT',
				'idTicket_venta' => 'INT',
				'idProducto' => 'INT',
				'cantidad' => 'STR',
				'descuento' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> [],

			'autoincrement' 	=> null,

			'nullable'			=> ['idTicket_venta', 'idProducto', 'cantidad', 'descuento'],

			'required'			=> ['idTicket_venta_detalle'],

			'uniques'			=> [],

			'rules' 			=> [
				'idTicket_venta_detalle' => ['type' => 'int', 'required' => true],
				'idTicket_venta' => ['type' => 'int'],
				'idProducto' => ['type' => 'int'],
				'cantidad' => ['type' => 'decimal(10,3)'],
				'descuento' => ['type' => 'int']
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

