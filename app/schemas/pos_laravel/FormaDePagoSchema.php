<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class FormaDePagoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'forma_de_pago',

			'id_name'			=> 'idFormaPago',

			'fields'			=> ['idFormaPago', 'nombre', 'activo', 'idFormaPagoSii'],

			'attr_types'		=> [
				'idFormaPago' => 'INT',
				'nombre' => 'STR',
				'activo' => 'INT',
				'idFormaPagoSii' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idFormaPago'],

			'autoincrement' 	=> 'idFormaPago',

			'nullable'			=> ['idFormaPago', 'activo'],

			'required'			=> ['nombre', 'idFormaPagoSii'],

			'uniques'			=> [],

			'rules' 			=> [
				'idFormaPago' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 50, 'required' => true],
				'activo' => ['type' => 'int'],
				'idFormaPagoSii' => ['type' => 'int', 'required' => true]
			],

			'fks' 				=> [],

			'relationships' => [
				
			],

			'expanded_relationships' => array (
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}

