<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class VentaPackSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'venta_pack',

			'id_name'			=> 'idTipo_producto',

			'fields'			=> ['idVenta_pack', 'idProducto', 'idCategoria', 'idTipo_producto', 'nombre', 'estado'],

			'attr_types'		=> [
				'idVenta_pack' => 'INT',
				'idProducto' => 'INT',
				'idCategoria' => 'INT',
				'idTipo_producto' => 'INT',
				'nombre' => 'STR',
				'estado' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idVenta_pack', 'idProducto', 'idCategoria', 'idTipo_producto'],

			'autoincrement' 	=> null,

			'nullable'			=> ['nombre', 'estado'],

			'required'			=> ['idVenta_pack', 'idProducto', 'idCategoria', 'idTipo_producto'],

			'uniques'			=> [],

			'rules' 			=> [
				'idVenta_pack' => ['type' => 'int', 'required' => true],
				'idProducto' => ['type' => 'int', 'required' => true],
				'idCategoria' => ['type' => 'int', 'required' => true],
				'idTipo_producto' => ['type' => 'int', 'required' => true],
				'nombre' => ['type' => 'str', 'max' => 45],
				'estado' => ['type' => 'int']
			],

			'fks' 				=> ['idCategoria', 'idProducto', 'idTipo_producto'],

			'relationships' => [
				'articulo' => [
					['articulo.idCategoria','venta_pack.idCategoria'],
					['articulo.idProducto','venta_pack.idProducto'],
					['articulo.idTipo_producto','venta_pack.idTipo_producto']
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
        1 => 'idCategoria',
      ),
      1 => 
      array (
        0 => 'venta_pack',
        1 => 'idCategoria',
      ),
    ),
    1 => 
    array (
      0 => 
      array (
        0 => 'articulo',
        1 => 'idProducto',
      ),
      1 => 
      array (
        0 => 'venta_pack',
        1 => 'idProducto',
      ),
    ),
    2 => 
    array (
      0 => 
      array (
        0 => 'articulo',
        1 => 'idTipo_producto',
      ),
      1 => 
      array (
        0 => 'venta_pack',
        1 => 'idTipo_producto',
      ),
    ),
  ),
),

			'relationships_from' => [
				'articulo' => [
					['articulo.idCategoria','venta_pack.idCategoria'],
					['articulo.idProducto','venta_pack.idProducto'],
					['articulo.idTipo_producto','venta_pack.idTipo_producto']
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
        1 => 'idCategoria',
      ),
      1 => 
      array (
        0 => 'venta_pack',
        1 => 'idCategoria',
      ),
    ),
    1 => 
    array (
      0 => 
      array (
        0 => 'articulo',
        1 => 'idProducto',
      ),
      1 => 
      array (
        0 => 'venta_pack',
        1 => 'idProducto',
      ),
    ),
    2 => 
    array (
      0 => 
      array (
        0 => 'articulo',
        1 => 'idTipo_producto',
      ),
      1 => 
      array (
        0 => 'venta_pack',
        1 => 'idTipo_producto',
      ),
    ),
  ),
)
		];
	}	
}

