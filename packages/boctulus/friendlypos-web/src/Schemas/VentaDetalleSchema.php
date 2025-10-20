<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class VentaDetalleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'venta_detalle',

			'id_name'			=> 'idDetalle_venta',

			'fields'			=> ['idDetalle_venta', 'idVenta', 'idProducto', 'cantidad', 'descuento', 'afecto', 'total', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idDetalle_venta' => 'INT',
				'idVenta' => 'INT',
				'idProducto' => 'INT',
				'cantidad' => 'STR',
				'descuento' => 'INT',
				'afecto' => 'INT',
				'total' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idDetalle_venta', 'idVenta', 'idProducto'],

			'autoincrement' 	=> 'idDetalle_venta',

			'nullable'			=> ['idDetalle_venta', 'cantidad', 'descuento', 'afecto', 'total', 'created_at', 'updated_at'],

			'required'			=> ['idVenta', 'idProducto'],

			'uniques'			=> [],

			'rules' 			=> [
				'idDetalle_venta' => ['type' => 'int'],
				'idVenta' => ['type' => 'int', 'required' => true],
				'idProducto' => ['type' => 'int', 'required' => true],
				'cantidad' => ['type' => 'decimal(10,3)'],
				'descuento' => ['type' => 'int'],
				'afecto' => ['type' => 'int'],
				'total' => ['type' => 'int'],
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

