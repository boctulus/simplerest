<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class SucursalSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'sucursal',

			'id_name'		=> 'chr_sucucodigo',

			'attr_types'	=> [
				'chr_sucucodigo' => 'STR',
				'vch_sucunombre' => 'STR',
				'vch_sucuciudad' => 'STR',
				'vch_sucudireccion' => 'STR',
				'int_sucucontcuenta' => 'INT'
			],

			'primary'		=> ['chr_sucucodigo'],

			'autoincrement' => null,

			'nullable'		=> ['vch_sucudireccion'],

			'uniques'		=> [],

			'rules' 		=> [
				'chr_sucucodigo' => ['type' => 'str', 'required' => true],
				'vch_sucunombre' => ['type' => 'str', 'max' => 50, 'required' => true],
				'vch_sucuciudad' => ['type' => 'str', 'max' => 30, 'required' => true],
				'vch_sucudireccion' => ['type' => 'str', 'max' => 50],
				'int_sucucontcuenta' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> [],

			'relationships' => [
				'asignado' => [
					['asignado.chr_sucucodigo','sucursal.chr_sucucodigo']
				],
				'cuenta' => [
					['cuenta.chr_sucucodigo','sucursal.chr_sucucodigo']
				]
			],

			'expanded_relationships' => array (
  'asignado' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'asignado',
        1 => 'chr_sucucodigo',
      ),
      1 => 
      array (
        0 => 'sucursal',
        1 => 'chr_sucucodigo',
      ),
    ),
  ),
  'cuenta' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'cuenta',
        1 => 'chr_sucucodigo',
      ),
      1 => 
      array (
        0 => 'sucursal',
        1 => 'chr_sucucodigo',
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

