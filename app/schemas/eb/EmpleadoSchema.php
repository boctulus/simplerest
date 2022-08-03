<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class EmpleadoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'empleado',

			'id_name'		=> 'chr_emplcodigo',

			'attr_types'	=> [
				'chr_emplcodigo' => 'STR',
				'vch_emplpaterno' => 'STR',
				'vch_emplmaterno' => 'STR',
				'vch_emplnombre' => 'STR',
				'vch_emplciudad' => 'STR',
				'vch_empldireccion' => 'STR'
			],

			'primary'		=> ['chr_emplcodigo'],

			'autoincrement' => null,

			'nullable'		=> ['vch_empldireccion'],

			'uniques'		=> [],

			'rules' 		=> [
				'chr_emplcodigo' => ['type' => 'str', 'required' => true],
				'vch_emplpaterno' => ['type' => 'str', 'max' => 25, 'required' => true],
				'vch_emplmaterno' => ['type' => 'str', 'max' => 25, 'required' => true],
				'vch_emplnombre' => ['type' => 'str', 'max' => 30, 'required' => true],
				'vch_emplciudad' => ['type' => 'str', 'max' => 30, 'required' => true],
				'vch_empldireccion' => ['type' => 'str', 'max' => 50]
			],

			'fks' 			=> [],

			'relationships' => [
				'asignado' => [
					['asignado.chr_emplcodigo','empleado.chr_emplcodigo']
				],
				'cuenta' => [
					['cuenta.chr_emplcreacuenta','empleado.chr_emplcodigo']
				],
				'logsession' => [
					['logsession.chr_emplcodigo','empleado.chr_emplcodigo']
				],
				'movimiento' => [
					['movimiento.chr_emplcodigo','empleado.chr_emplcodigo']
				],
				'usuario' => [
					['usuario.chr_emplcodigo','empleado.chr_emplcodigo']
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
        1 => 'chr_emplcodigo',
      ),
      1 => 
      array (
        0 => 'empleado',
        1 => 'chr_emplcodigo',
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
        1 => 'chr_emplcreacuenta',
      ),
      1 => 
      array (
        0 => 'empleado',
        1 => 'chr_emplcodigo',
      ),
    ),
  ),
  'logsession' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'logsession',
        1 => 'chr_emplcodigo',
      ),
      1 => 
      array (
        0 => 'empleado',
        1 => 'chr_emplcodigo',
      ),
    ),
  ),
  'movimiento' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'movimiento',
        1 => 'chr_emplcodigo',
      ),
      1 => 
      array (
        0 => 'empleado',
        1 => 'chr_emplcodigo',
      ),
    ),
  ),
  'usuario' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'usuario',
        1 => 'chr_emplcodigo',
      ),
      1 => 
      array (
        0 => 'empleado',
        1 => 'chr_emplcodigo',
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

