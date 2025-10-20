<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ArticuloSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'articulo',

			'id_name'			=> 'idProducto',

			'fields'			=> ['idProducto', 'idCategoria', 'idTipo_producto', 'afecto_', 'nombre', 'descripcion', 'costo_', 'precio_', 'activo', 'foto', 'marca', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idProducto' => 'INT',
				'idCategoria' => 'INT',
				'idTipo_producto' => 'INT',
				'afecto_' => 'INT',
				'nombre' => 'STR',
				'descripcion' => 'STR',
				'costo_' => 'INT',
				'precio_' => 'INT',
				'activo' => 'INT',
				'foto' => 'STR',
				'marca' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idProducto', 'idCategoria', 'idTipo_producto'],

			'autoincrement' 	=> 'idProducto',

			'nullable'			=> ['idProducto', 'afecto_', 'precio_', 'activo', 'foto', 'marca', 'created_at', 'updated_at'],

			'required'			=> ['idCategoria', 'idTipo_producto', 'nombre', 'descripcion', 'costo_'],

			'uniques'			=> [],

			'rules' 			=> [
				'idProducto' => ['type' => 'int'],
				'idCategoria' => ['type' => 'int', 'required' => true],
				'idTipo_producto' => ['type' => 'int', 'required' => true],
				'afecto_' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 45, 'required' => true],
				'descripcion' => ['type' => 'str', 'required' => true],
				'costo_' => ['type' => 'int', 'required' => true],
				'precio_' => ['type' => 'int'],
				'activo' => ['type' => 'int'],
				'foto' => ['type' => 'str', 'max' => 200],
				'marca' => ['type' => 'str', 'max' => 200],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['idCategoria'],

			'relationships' => [
				'categoria' => [
					['categoria.idCategoria','articulo.idCategoria']
				],
				'venta_pack' => [
					['venta_pack.idCategoria','articulo.idCategoria'],
					['venta_pack.idProducto','articulo.idProducto'],
					['venta_pack.idTipo_producto','articulo.idTipo_producto']
				],
				'carrito_detalle' => [
					['carrito_detalle.idProducto','articulo.idProducto']
				],
				'carrito_detalle_item_extra' => [
					['carrito_detalle_item_extra.idProducto','articulo.idProducto']
				],
				'detalle_venta' => [
					['detalle_venta.idProducto','articulo.idProducto']
				],
				'empresa_producto' => [
					['empresa_producto.idProducto','articulo.idProducto']
				]
			],

			'expanded_relationships' => array (
  'categoria' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'categoria',
        1 => 'idCategoria',
      ),
      1 => 
      array (
        0 => 'articulo',
        1 => 'idCategoria',
      ),
    ),
  ),
  'venta_pack' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'venta_pack',
        1 => 'idCategoria',
      ),
      1 => 
      array (
        0 => 'articulo',
        1 => 'idCategoria',
      ),
    ),
    1 => 
    array (
      0 => 
      array (
        0 => 'venta_pack',
        1 => 'idProducto',
      ),
      1 => 
      array (
        0 => 'articulo',
        1 => 'idProducto',
      ),
    ),
    2 => 
    array (
      0 => 
      array (
        0 => 'venta_pack',
        1 => 'idTipo_producto',
      ),
      1 => 
      array (
        0 => 'articulo',
        1 => 'idTipo_producto',
      ),
    ),
  ),
  'carrito_detalle' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'carrito_detalle',
        1 => 'idProducto',
      ),
      1 => 
      array (
        0 => 'articulo',
        1 => 'idProducto',
      ),
    ),
  ),
  'carrito_detalle_item_extra' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'carrito_detalle_item_extra',
        1 => 'idProducto',
      ),
      1 => 
      array (
        0 => 'articulo',
        1 => 'idProducto',
      ),
    ),
  ),
  'detalle_venta' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'detalle_venta',
        1 => 'idProducto',
      ),
      1 => 
      array (
        0 => 'articulo',
        1 => 'idProducto',
      ),
    ),
  ),
  'empresa_producto' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'empresa_producto',
        1 => 'idProducto',
      ),
      1 => 
      array (
        0 => 'articulo',
        1 => 'idProducto',
      ),
    ),
  ),
),

			'relationships_from' => [
				'categoria' => [
					['categoria.idCategoria','articulo.idCategoria']
				]
			],

			'expanded_relationships_from' => array (
  'categoria' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'categoria',
        1 => 'idCategoria',
      ),
      1 => 
      array (
        0 => 'articulo',
        1 => 'idCategoria',
      ),
    ),
  ),
)
		];
	}	
}

