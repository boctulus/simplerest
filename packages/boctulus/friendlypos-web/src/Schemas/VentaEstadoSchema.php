<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class VentaEstadoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'venta_estado',

			'id_name'			=> 'idVenta_estado',

			'fields'			=> ['idVenta_estado', 'nombre', 'descripcion'],

			'attr_types'		=> [
				'idVenta_estado' => 'INT',
				'nombre' => 'STR',
				'descripcion' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idVenta_estado'],

			'autoincrement' 	=> 'idVenta_estado',

			'nullable'			=> ['idVenta_estado', 'nombre', 'descripcion'],

			'required'			=> [],

			'uniques'			=> [],

			'rules' 			=> [
				'idVenta_estado' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 45],
				'descripcion' => ['type' => 'str', 'max' => 45]
			],

			'fks' 				=> [],

			'relationships' => [
				'venta' => [
					['venta.idVenta_estado','venta_estado.idVenta_estado']
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
        1 => 'idVenta_estado',
      ),
      1 => 
      array (
        0 => 'venta_estado',
        1 => 'idVenta_estado',
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

