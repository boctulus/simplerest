<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class TicketVentaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'ticket_venta',

			'id_name'			=> 'idTicket_venta',

			'fields'			=> ['idTicket_venta', 'idCarrito', 'idCliente', 'idEmpresa', 'nota', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idTicket_venta' => 'INT',
				'idCarrito' => 'INT',
				'idCliente' => 'INT',
				'idEmpresa' => 'INT',
				'nota' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idTicket_venta'],

			'autoincrement' 	=> 'idTicket_venta',

			'nullable'			=> ['idTicket_venta', 'idCarrito', 'idCliente', 'idEmpresa', 'nota', 'created_at', 'updated_at'],

			'required'			=> [],

			'uniques'			=> [],

			'rules' 			=> [
				'idTicket_venta' => ['type' => 'int'],
				'idCarrito' => ['type' => 'int'],
				'idCliente' => ['type' => 'int'],
				'idEmpresa' => ['type' => 'int'],
				'nota' => ['type' => 'str'],
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

