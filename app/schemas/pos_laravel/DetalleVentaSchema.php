<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class DetalleVentaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'detalle_venta',

			'id_name'			=> 'idProducto',

			'fields'			=> ['idDetalle_venta', 'idVenta', 'idProducto', 'cantidad', 'descuento', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idDetalle_venta' => 'INT',
				'idVenta' => 'INT',
				'idProducto' => 'INT',
				'cantidad' => 'INT',
				'descuento' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idDetalle_venta', 'idVenta', 'idProducto'],

			'autoincrement' 	=> null,

			'nullable'			=> ['cantidad', 'descuento', 'created_at', 'updated_at'],

			'required'			=> ['idDetalle_venta', 'idVenta', 'idProducto'],

			'uniques'			=> [],

			'rules' 			=> [
				'idDetalle_venta' => ['type' => 'int', 'required' => true],
				'idVenta' => ['type' => 'int', 'required' => true],
				'idProducto' => ['type' => 'int', 'required' => true],
				'cantidad' => ['type' => 'int'],
				'descuento' => ['type' => 'int'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['idProducto', 'idVenta'],

			'relationships' => [
				'articulo' => [
					['articulo.idProducto','detalle_venta.idProducto']
				],
				'venta' => [
					['venta.idVenta','detalle_venta.idVenta']
				],
				'descuentosrecargos' => [
					['descuentosrecargos.idDetalle_venta','detalle_venta.idDetalle_venta']
				]
			],

			'expanded_relationships' => array (
  'articulo' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'articulo',
        1 => 'idProducto',
      ),
      1 => 
      array (
        0 => 'detalle_venta',
        1 => 'idProducto',
      ),
    ),
  ),
  'venta' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'venta',
        1 => 'idVenta',
      ),
      1 => 
      array (
        0 => 'detalle_venta',
        1 => 'idVenta',
      ),
    ),
  ),
  'descuentosrecargos' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'descuentosrecargos',
        1 => 'idDetalle_venta',
      ),
      1 => 
      array (
        0 => 'detalle_venta',
        1 => 'idDetalle_venta',
      ),
    ),
  ),
),

			'relationships_from' => [
				'articulo' => [
					['articulo.idProducto','detalle_venta.idProducto']
				],
				'venta' => [
					['venta.idVenta','detalle_venta.idVenta']
				]
			],

			'expanded_relationships_from' => array (
  'articulo' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'articulo',
        1 => 'idProducto',
      ),
      1 => 
      array (
        0 => 'detalle_venta',
        1 => 'idProducto',
      ),
    ),
  ),
  'venta' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'venta',
        1 => 'idVenta',
      ),
      1 => 
      array (
        0 => 'detalle_venta',
        1 => 'idVenta',
      ),
    ),
  ),
)
		];
	}	
}

