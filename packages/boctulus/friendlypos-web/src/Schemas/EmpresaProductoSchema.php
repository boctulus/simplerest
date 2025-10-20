<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class EmpresaProductoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'empresa_producto',

			'id_name'			=> 'idEmpresa_producto',

			'fields'			=> ['idEmpresa_producto', 'idEmpresa', 'idProducto', 'idCategoria', 'codigo_barra', 'sku', 'afecto', 'es_pack', 'activo', 'stock', 'stock_critico', 'notifica_stock_critico', 'precio', 'costo', 'idUnidad', 'marca', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idEmpresa_producto' => 'INT',
				'idEmpresa' => 'INT',
				'idProducto' => 'INT',
				'idCategoria' => 'INT',
				'codigo_barra' => 'STR',
				'sku' => 'STR',
				'afecto' => 'INT',
				'es_pack' => 'INT',
				'activo' => 'INT',
				'stock' => 'INT',
				'stock_critico' => 'INT',
				'notifica_stock_critico' => 'INT',
				'precio' => 'INT',
				'costo' => 'INT',
				'idUnidad' => 'INT',
				'marca' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idEmpresa_producto'],

			'autoincrement' 	=> 'idEmpresa_producto',

			'nullable'			=> ['idEmpresa_producto', 'idCategoria', 'codigo_barra', 'sku', 'afecto', 'es_pack', 'activo', 'notifica_stock_critico', 'precio', 'costo', 'idUnidad', 'marca', 'created_at', 'updated_at'],

			'required'			=> ['idEmpresa', 'idProducto', 'stock', 'stock_critico'],

			'uniques'			=> [],

			'rules' 			=> [
				'idEmpresa_producto' => ['type' => 'int'],
				'idEmpresa' => ['type' => 'int', 'required' => true],
				'idProducto' => ['type' => 'int', 'required' => true],
				'idCategoria' => ['type' => 'int'],
				'codigo_barra' => ['type' => 'str', 'max' => 100],
				'sku' => ['type' => 'str', 'max' => 200],
				'afecto' => ['type' => 'int'],
				'es_pack' => ['type' => 'int'],
				'activo' => ['type' => 'int'],
				'stock' => ['type' => 'int', 'required' => true],
				'stock_critico' => ['type' => 'int', 'required' => true],
				'notifica_stock_critico' => ['type' => 'int'],
				'precio' => ['type' => 'int'],
				'costo' => ['type' => 'int'],
				'idUnidad' => ['type' => 'int'],
				'marca' => ['type' => 'str', 'max' => 50],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['idEmpresa', 'idProducto'],

			'relationships' => [
				'empresa' => [
					['empresa.idEmpresa','empresa_producto.idEmpresa']
				],
				'articulo' => [
					['articulo.idProducto','empresa_producto.idProducto']
				],
				'articulo_seleccionable' => [
					['articulo_seleccionable.idEmpresa_producto','empresa_producto.idEmpresa_producto'],
					['articulo_seleccionable.idProducto','empresa_producto.idProducto']
				]
			],

			'expanded_relationships' => array (
  'empresa' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'empresa',
        1 => 'idEmpresa',
      ),
      1 => 
      array (
        0 => 'empresa_producto',
        1 => 'idEmpresa',
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
        0 => 'empresa_producto',
        1 => 'idProducto',
      ),
    ),
  ),
  'articulo_seleccionable' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'articulo_seleccionable',
        1 => 'idEmpresa_producto',
      ),
      1 => 
      array (
        0 => 'empresa_producto',
        1 => 'idEmpresa_producto',
      ),
    ),
    1 => 
    array (
      0 => 
      array (
        0 => 'articulo_seleccionable',
        1 => 'idProducto',
      ),
      1 => 
      array (
        0 => 'empresa_producto',
        1 => 'idProducto',
      ),
    ),
  ),
),

			'relationships_from' => [
				'empresa' => [
					['empresa.idEmpresa','empresa_producto.idEmpresa']
				],
				'articulo' => [
					['articulo.idProducto','empresa_producto.idProducto']
				]
			],

			'expanded_relationships_from' => array (
  'empresa' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'empresa',
        1 => 'idEmpresa',
      ),
      1 => 
      array (
        0 => 'empresa_producto',
        1 => 'idEmpresa',
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
        0 => 'empresa_producto',
        1 => 'idProducto',
      ),
    ),
  ),
)
		];
	}	
}

