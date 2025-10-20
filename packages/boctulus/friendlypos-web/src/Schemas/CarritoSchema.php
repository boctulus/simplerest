<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class CarritoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'carrito',

			'id_name'			=> 'idCarrito',

			'fields'			=> ['idCarrito', 'idEmpresa', 'idCliente', 'idDocumentoDte', 'fecha', 'activo', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idCarrito' => 'INT',
				'idEmpresa' => 'INT',
				'idCliente' => 'INT',
				'idDocumentoDte' => 'INT',
				'fecha' => 'STR',
				'activo' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idCarrito', 'idCliente'],

			'autoincrement' 	=> 'idCarrito',

			'nullable'			=> ['idCarrito', 'idEmpresa', 'idDocumentoDte', 'fecha', 'activo', 'created_at', 'updated_at'],

			'required'			=> ['idCliente'],

			'uniques'			=> [],

			'rules' 			=> [
				'idCarrito' => ['type' => 'int'],
				'idEmpresa' => ['type' => 'int'],
				'idCliente' => ['type' => 'int', 'required' => true],
				'idDocumentoDte' => ['type' => 'int'],
				'fecha' => ['type' => 'timestamp'],
				'activo' => ['type' => 'int'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> ['idCliente'],

			'relationships' => [
				'cliente' => [
					['cliente.idCliente','carrito.idCliente']
				],
				'carrito_detalle' => [
					['carrito_detalle.idCarrito','carrito.idCarrito']
				],
				'carrito_detalle_item_extra' => [
					['carrito_detalle_item_extra.idCarrito','carrito.idCarrito']
				]
			],

			'expanded_relationships' => array (
  'cliente' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'cliente',
        1 => 'idCliente',
      ),
      1 => 
      array (
        0 => 'carrito',
        1 => 'idCliente',
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
        1 => 'idCarrito',
      ),
      1 => 
      array (
        0 => 'carrito',
        1 => 'idCarrito',
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
        1 => 'idCarrito',
      ),
      1 => 
      array (
        0 => 'carrito',
        1 => 'idCarrito',
      ),
    ),
  ),
),

			'relationships_from' => [
				'cliente' => [
					['cliente.idCliente','carrito.idCliente']
				]
			],

			'expanded_relationships_from' => array (
  'cliente' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'cliente',
        1 => 'idCliente',
      ),
      1 => 
      array (
        0 => 'carrito',
        1 => 'idCliente',
      ),
    ),
  ),
)
		];
	}	
}

