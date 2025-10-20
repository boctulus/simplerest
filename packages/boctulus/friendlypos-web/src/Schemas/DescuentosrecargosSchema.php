<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class DescuentosrecargosSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'descuentosrecargos',

			'id_name'			=> 'idDetalle_venta',

			'fields'			=> ['idDescuentosRecargos', 'idDetalle_venta', 'monto'],

			'attr_types'		=> [
				'idDescuentosRecargos' => 'INT',
				'idDetalle_venta' => 'INT',
				'monto' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idDescuentosRecargos', 'idDetalle_venta'],

			'autoincrement' 	=> null,

			'nullable'			=> ['monto'],

			'required'			=> ['idDescuentosRecargos', 'idDetalle_venta'],

			'uniques'			=> [],

			'rules' 			=> [
				'idDescuentosRecargos' => ['type' => 'int', 'required' => true],
				'idDetalle_venta' => ['type' => 'int', 'required' => true],
				'monto' => ['type' => 'int']
			],

			'fks' 				=> ['idDetalle_venta'],

			'relationships' => [
				'detalle_venta' => [
					['detalle_venta.idDetalle_venta','descuentosrecargos.idDetalle_venta']
				]
			],

			'expanded_relationships' => array (
  'detalle_venta' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'detalle_venta',
        1 => 'idDetalle_venta',
      ),
      1 => 
      array (
        0 => 'descuentosrecargos',
        1 => 'idDetalle_venta',
      ),
    ),
  ),
),

			'relationships_from' => [
				'detalle_venta' => [
					['detalle_venta.idDetalle_venta','descuentosrecargos.idDetalle_venta']
				]
			],

			'expanded_relationships_from' => array (
  'detalle_venta' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'detalle_venta',
        1 => 'idDetalle_venta',
      ),
      1 => 
      array (
        0 => 'descuentosrecargos',
        1 => 'idDetalle_venta',
      ),
    ),
  ),
)
		];
	}	
}

