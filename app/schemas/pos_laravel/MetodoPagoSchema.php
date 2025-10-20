<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class MetodoPagoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'metodo_pago',

			'id_name'			=> 'idMetodo_pago',

			'fields'			=> ['idMetodo_pago', 'nombre'],

			'attr_types'		=> [
				'idMetodo_pago' => 'INT',
				'nombre' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idMetodo_pago'],

			'autoincrement' 	=> 'idMetodo_pago',

			'nullable'			=> ['idMetodo_pago', 'nombre'],

			'required'			=> [],

			'uniques'			=> ['nombre'],

			'rules' 			=> [
				'idMetodo_pago' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 45]
			],

			'fks' 				=> [],

			'relationships' => [
				'venta' => [
					['venta.idMetodo_pago','metodo_pago.idMetodo_pago']
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
        1 => 'idMetodo_pago',
      ),
      1 => 
      array (
        0 => 'metodo_pago',
        1 => 'idMetodo_pago',
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

