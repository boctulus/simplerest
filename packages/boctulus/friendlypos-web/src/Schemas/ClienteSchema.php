<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ClienteSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'cliente',

			'id_name'			=> 'idCliente',

			'fields'			=> ['idCliente', 'nombre', 'rut', 'activo', 'direccion', 'email', 'tipo_cliente', 'tipo_recep', 'rut_recep', 'rzn_soc_recep', 'giro_recep', 'dir_recep', 'cmna_recep', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'idCliente' => 'INT',
				'nombre' => 'STR',
				'rut' => 'STR',
				'activo' => 'INT',
				'direccion' => 'STR',
				'email' => 'STR',
				'tipo_cliente' => 'STR',
				'tipo_recep' => 'INT',
				'rut_recep' => 'STR',
				'rzn_soc_recep' => 'STR',
				'giro_recep' => 'STR',
				'dir_recep' => 'STR',
				'cmna_recep' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idCliente'],

			'autoincrement' 	=> 'idCliente',

			'nullable'			=> ['idCliente', 'nombre', 'rut', 'activo', 'tipo_recep', 'created_at', 'updated_at'],

			'required'			=> ['direccion', 'email', 'tipo_cliente', 'rut_recep', 'rzn_soc_recep', 'giro_recep', 'dir_recep', 'cmna_recep'],

			'uniques'			=> ['rut'],

			'rules' 			=> [
				'idCliente' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 45],
				'rut' => ['type' => 'str', 'max' => 45],
				'activo' => ['type' => 'int'],
				'direccion' => ['type' => 'str', 'max' => 100, 'required' => true],
				'email' => ['type' => 'str', 'max' => 100, 'required' => true],
				'tipo_cliente' => ['type' => 'str', 'max' => 20, 'required' => true],
				'tipo_recep' => ['type' => 'int'],
				'rut_recep' => ['type' => 'str', 'max' => 20, 'required' => true],
				'rzn_soc_recep' => ['type' => 'str', 'max' => 200, 'required' => true],
				'giro_recep' => ['type' => 'str', 'max' => 200, 'required' => true],
				'dir_recep' => ['type' => 'str', 'max' => 100, 'required' => true],
				'cmna_recep' => ['type' => 'str', 'max' => 50, 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 				=> [],

			'relationships' => [
				'carrito' => [
					['carrito.idCliente','cliente.idCliente']
				],
				'venta' => [
					['venta.idCliente','cliente.idCliente']
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
        1 => 'idCliente',
      ),
      1 => 
      array (
        0 => 'cliente',
        1 => 'idCliente',
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
        1 => 'idCliente',
      ),
      1 => 
      array (
        0 => 'cliente',
        1 => 'idCliente',
      ),
    ),
  ),
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}

