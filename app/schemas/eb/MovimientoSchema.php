<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class MovimientoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'movimiento',

			'id_name'		=> 'int_movinumero',

			'attr_types'	=> [
				'chr_cuencodigo' => 'STR',
				'int_movinumero' => 'INT',
				'dtt_movifecha' => 'STR',
				'chr_emplcodigo' => 'STR',
				'chr_tipocodigo' => 'STR',
				'dec_moviimporte' => 'STR',
				'chr_cuenreferencia' => 'STR'
			],

			'primary'		=> ['chr_cuencodigo', 'int_movinumero'],

			'autoincrement' => null,

			'nullable'		=> ['chr_cuenreferencia'],

			'uniques'		=> [],

			'rules' 		=> [
				'chr_cuencodigo' => ['type' => 'str', 'required' => true],
				'int_movinumero' => ['type' => 'int', 'required' => true],
				'dtt_movifecha' => ['type' => 'date', 'required' => true],
				'chr_emplcodigo' => ['type' => 'str', 'required' => true],
				'chr_tipocodigo' => ['type' => 'str', 'required' => true],
				'dec_moviimporte' => ['type' => 'decimal(12,2)', 'required' => true],
				'chr_cuenreferencia' => ['type' => 'str']
			],

			'fks' 			=> ['chr_cuencodigo', 'chr_emplcodigo', 'chr_tipocodigo'],

			'relationships' => [
				'cuenta' => [
					['cuenta.chr_cuencodigo','movimiento.chr_cuencodigo']
				],
				'empleado' => [
					['empleado.chr_emplcodigo','movimiento.chr_emplcodigo']
				],
				'tipomovimiento' => [
					['tipomovimiento.chr_tipocodigo','movimiento.chr_tipocodigo']
				]
			],

			'expanded_relationships' => array (
  'cuenta' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'cuenta',
        1 => 'chr_cuencodigo',
      ),
      1 => 
      array (
        0 => 'movimiento',
        1 => 'chr_cuencodigo',
      ),
    ),
  ),
  'empleado' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'empleado',
        1 => 'chr_emplcodigo',
      ),
      1 => 
      array (
        0 => 'movimiento',
        1 => 'chr_emplcodigo',
      ),
    ),
  ),
  'tipomovimiento' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'tipomovimiento',
        1 => 'chr_tipocodigo',
      ),
      1 => 
      array (
        0 => 'movimiento',
        1 => 'chr_tipocodigo',
      ),
    ),
  ),
),

			'relationships_from' => [
				'cuenta' => [
					['cuenta.chr_cuencodigo','movimiento.chr_cuencodigo']
				],
				'empleado' => [
					['empleado.chr_emplcodigo','movimiento.chr_emplcodigo']
				],
				'tipomovimiento' => [
					['tipomovimiento.chr_tipocodigo','movimiento.chr_tipocodigo']
				]
			],

			'expanded_relationships_from' => array (
  'cuenta' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'cuenta',
        1 => 'chr_cuencodigo',
      ),
      1 => 
      array (
        0 => 'movimiento',
        1 => 'chr_cuencodigo',
      ),
    ),
  ),
  'empleado' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'empleado',
        1 => 'chr_emplcodigo',
      ),
      1 => 
      array (
        0 => 'movimiento',
        1 => 'chr_emplcodigo',
      ),
    ),
  ),
  'tipomovimiento' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'tipomovimiento',
        1 => 'chr_tipocodigo',
      ),
      1 => 
      array (
        0 => 'movimiento',
        1 => 'chr_tipocodigo',
      ),
    ),
  ),
)
		];
	}	
}

