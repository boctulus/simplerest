<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ReferenciaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'referencia',

			'id_name'			=> 'idVenta',

			'fields'			=> ['idReferencia', 'idVenta', 'razon'],

			'attr_types'		=> [
				'idReferencia' => 'INT',
				'idVenta' => 'INT',
				'razon' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idReferencia', 'idVenta'],

			'autoincrement' 	=> null,

			'nullable'			=> ['razon'],

			'required'			=> ['idReferencia', 'idVenta'],

			'uniques'			=> [],

			'rules' 			=> [
				'idReferencia' => ['type' => 'int', 'required' => true],
				'idVenta' => ['type' => 'int', 'required' => true],
				'razon' => ['type' => 'str', 'max' => 45]
			],

			'fks' 				=> ['idVenta'],

			'relationships' => [
				'venta' => [
					['venta.idVenta','referencia.idVenta']
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
        1 => 'idVenta',
      ),
      1 => 
      array (
        0 => 'referencia',
        1 => 'idVenta',
      ),
    ),
  ),
),

			'relationships_from' => [
				'venta' => [
					['venta.idVenta','referencia.idVenta']
				]
			],

			'expanded_relationships_from' => array (
  'venta' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'venta',
        1 => 'idVenta',
      ),
      1 => 
      array (
        0 => 'referencia',
        1 => 'idVenta',
      ),
    ),
  ),
)
		];
	}	
}

