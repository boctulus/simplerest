<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CarritoDetalleItemExtraSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'carrito_detalle_item_extra',

			'id_name'			=> 'idCarrito_detalle_item_extra',

			'fields'			=> ['idCarrito_detalle_item_extra', 'idCarrito', 'idProducto', 'cantidad', 'precio', 'descuento', 'nombre', 'descripcion', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idCarrito_detalle_item_extra' => 'INT',
				'idCarrito' => 'INT',
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

			'primary'			=> ['idCarrito_detalle_item_extra', 'idCarrito', 'idProducto'],

			'autoincrement' 	=> 'idCarrito_detalle_item_extra',

			'nullable'			=> ['idCarrito_detalle_item_extra', 'cantidad', 'precio', 'descuento', 'nombre', 'descripcion', 'created_at', 'updated_at'],

			'required'			=> ['idCarrito', 'idProducto'],

			'uniques'			=> [],

			'rules' 			=> [
				'idCarrito_detalle_item_extra' => ['type' => 'int'],
				'idCarrito' => ['type' => 'int', 'required' => true],
				'idProducto' => ['type' => 'int', 'required' => true],
				'cantidad' => ['type' => 'int'],
				'precio' => ['type' => 'int'],
				'descuento' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 100],
				'descripcion' => ['type' => 'str', 'max' => 50],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['idCarrito', 'idProducto'],

			'relationships' => [
				'carrito' => [
					['carrito.idCarrito','carrito_detalle_item_extra.idCarrito']
				],
				'articulo' => [
					['articulo.idProducto','carrito_detalle_item_extra.idProducto']
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
        0 => 'carrito_detalle_item_extra',
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
        0 => 'carrito_detalle_item_extra',
        1 => 'idProducto',
      ),
    ),
  ),
),

			'relationships_from' => [
				'carrito' => [
					['carrito.idCarrito','carrito_detalle_item_extra.idCarrito']
				],
				'articulo' => [
					['articulo.idProducto','carrito_detalle_item_extra.idProducto']
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
        0 => 'carrito_detalle_item_extra',
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
        0 => 'carrito_detalle_item_extra',
        1 => 'idProducto',
      ),
    ),
  ),
)
		];
	}	
}

