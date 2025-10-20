<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ArticuloSeleccionableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'articulo_seleccionable',

			'id_name'			=> 'idArticuloSeleccionable',

			'fields'			=> ['idArticuloSeleccionable', 'idPack', 'idEmpresa_producto', 'idProducto'],

			'attr_types'		=> [
				'idArticuloSeleccionable' => 'INT',
				'idPack' => 'INT',
				'idEmpresa_producto' => 'INT',
				'idProducto' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idArticuloSeleccionable'],

			'autoincrement' 	=> 'idArticuloSeleccionable',

			'nullable'			=> ['idArticuloSeleccionable', 'idEmpresa_producto'],

			'required'			=> ['idPack', 'idProducto'],

			'uniques'			=> [],

			'rules' 			=> [
				'idArticuloSeleccionable' => ['type' => 'int'],
				'idPack' => ['type' => 'int', 'required' => true],
				'idEmpresa_producto' => ['type' => 'int'],
				'idProducto' => ['type' => 'int', 'required' => true]
			],

			'fks' 				=> ['idEmpresa_producto', 'idProducto'],

			'relationships' => [
				'empresa_producto' => [
					['empresa_producto.idEmpresa_producto','articulo_seleccionable.idEmpresa_producto'],
					['empresa_producto.idProducto','articulo_seleccionable.idProducto']
				]
			],

			'expanded_relationships' => array (
  'empresa_producto' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'empresa_producto',
        1 => 'idEmpresa_producto',
      ),
      1 => 
      array (
        0 => 'articulo_seleccionable',
        1 => 'idEmpresa_producto',
      ),
    ),
    1 => 
    array (
      0 => 
      array (
        0 => 'empresa_producto',
        1 => 'idProducto',
      ),
      1 => 
      array (
        0 => 'articulo_seleccionable',
        1 => 'idProducto',
      ),
    ),
  ),
),

			'relationships_from' => [
				'empresa_producto' => [
					['empresa_producto.idEmpresa_producto','articulo_seleccionable.idEmpresa_producto'],
					['empresa_producto.idProducto','articulo_seleccionable.idProducto']
				]
			],

			'expanded_relationships_from' => array (
  'empresa_producto' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'empresa_producto',
        1 => 'idEmpresa_producto',
      ),
      1 => 
      array (
        0 => 'articulo_seleccionable',
        1 => 'idEmpresa_producto',
      ),
    ),
    1 => 
    array (
      0 => 
      array (
        0 => 'empresa_producto',
        1 => 'idProducto',
      ),
      1 => 
      array (
        0 => 'articulo_seleccionable',
        1 => 'idProducto',
      ),
    ),
  ),
)
		];
	}	
}

