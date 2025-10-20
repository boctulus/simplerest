<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class VentaDetalleItemExtraSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'venta_detalle_item_extra',

			'id_name'			=> 'idDetalle_venta_item_extra',

			'fields'			=> ['idDetalle_venta_item_extra', 'idVenta', 'idProducto', 'cantidad', 'descuento', 'total', 'descripcion', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idDetalle_venta_item_extra' => 'INT',
				'idVenta' => 'INT',
				'idProducto' => 'INT',
				'cantidad' => 'INT',
				'descuento' => 'INT',
				'total' => 'INT',
				'descripcion' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idDetalle_venta_item_extra', 'idVenta', 'idProducto'],

			'autoincrement' 	=> 'idDetalle_venta_item_extra',

			'nullable'			=> ['idDetalle_venta_item_extra', 'cantidad', 'descuento', 'descripcion', 'created_at', 'updated_at'],

			'required'			=> ['idVenta', 'idProducto', 'total'],

			'uniques'			=> [],

			'rules' 			=> [
				'idDetalle_venta_item_extra' => ['type' => 'int'],
				'idVenta' => ['type' => 'int', 'required' => true],
				'idProducto' => ['type' => 'int', 'required' => true],
				'cantidad' => ['type' => 'int'],
				'descuento' => ['type' => 'int'],
				'total' => ['type' => 'int', 'required' => true],
				'descripcion' => ['type' => 'str', 'max' => 50],
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

