<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CarritoDetalleSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'carrito_detalle',

			'id_name'			=> 'idCarrito_detalle',

			'fields'			=> ['idCarrito_detalle', 'idCarrito', 'idProducto', 'cantidad', 'descuento', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idCarrito_detalle' => 'INT',
				'idCarrito' => 'INT',
				'idProducto' => 'INT',
				'cantidad' => 'STR',
				'descuento' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idCarrito_detalle', 'idCarrito', 'idProducto'],

			'autoincrement' 	=> 'idCarrito_detalle',

			'nullable'			=> ['idCarrito_detalle', 'cantidad', 'descuento', 'created_at', 'updated_at'],

			'required'			=> ['idCarrito', 'idProducto'],

			'uniques'			=> [],

			'rules' 			=> [
				'idCarrito_detalle' => ['type' => 'int'],
				'idCarrito' => ['type' => 'int', 'required' => true],
				'idProducto' => ['type' => 'int', 'required' => true],
				'cantidad' => ['type' => 'decimal(10,3)'],
				'descuento' => ['type' => 'int'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['idCarrito', 'idProducto'],

			'relationships' => [
				'carrito' => [
					['carrito.idCarrito','carrito_detalle.idCarrito']
				],
				'articulo' => [
					['articulo.idProducto','carrito_detalle.idProducto']
				]
			],

			'expanded_relationships' => array (
  'carrito' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'carrito',
        1 => 'idCarrito',
      ),
      1 => 
      array (
        0 => 'carrito_detalle',
        1 => 'idCarrito',
      ),
    ),
  ),
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
        0 => 'carrito_detalle',
        1 => 'idProducto',
      ),
    ),
  ),
),

			'relationships_from' => [
				'carrito' => [
					['carrito.idCarrito','carrito_detalle.idCarrito']
				],
				'articulo' => [
					['articulo.idProducto','carrito_detalle.idProducto']
				]
			],

			'expanded_relationships_from' => array (
  'carrito' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'carrito',
        1 => 'idCarrito',
      ),
      1 => 
      array (
        0 => 'carrito_detalle',
        1 => 'idCarrito',
      ),
    ),
  ),
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
        0 => 'carrito_detalle',
        1 => 'idProducto',
      ),
    ),
  ),
)
		];
	}	
}

