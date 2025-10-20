<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class EstadoPagoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'estado_pago',

			'id_name'			=> 'idEstado_pago',

			'fields'			=> ['idEstado_pago', 'nombre'],

			'attr_types'		=> [
				'idEstado_pago' => 'INT',
				'nombre' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idEstado_pago'],

			'autoincrement' 	=> 'idEstado_pago',

			'nullable'			=> ['idEstado_pago', 'nombre'],

			'required'			=> [],

			'uniques'			=> [],

			'rules' 			=> [
				'idEstado_pago' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 45]
			],

			'fks' 				=> [],

			'relationships' => [
				'venta' => [
					['venta.idEstado_pago','estado_pago.idEstado_pago']
				]
			],

			'expanded_relationships' => array (
  'venta' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'venta',
        1 => 'idEstado_pago',
      ),
      1 => 
      array (
        0 => 'estado_pago',
        1 => 'idEstado_pago',
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

