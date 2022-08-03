<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CargomantenimientoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'cargomantenimiento',

			'id_name'		=> 'chr_monecodigo',

			'attr_types'	=> [
				'chr_monecodigo' => 'STR',
				'dec_cargMontoMaximo' => 'STR',
				'dec_cargImporte' => 'STR'
			],

			'primary'		=> ['chr_monecodigo'],

			'autoincrement' => null,

			'nullable'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'chr_monecodigo' => ['type' => 'str', 'required' => true],
				'dec_cargMontoMaximo' => ['type' => 'decimal(12,2)', 'required' => true],
				'dec_cargImporte' => ['type' => 'decimal(12,2)', 'required' => true]
			],

			'fks' 			=> ['chr_monecodigo'],

			'relationships' => [
				'moneda' => [
					['moneda.chr_monecodigo','cargomantenimiento.chr_monecodigo']
				]
			],

			'expanded_relationships' => array (
  'moneda' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'moneda',
        1 => 'chr_monecodigo',
      ),
      1 => 
      array (
        0 => 'cargomantenimiento',
        1 => 'chr_monecodigo',
      ),
    ),
  ),
),

			'relationships_from' => [
				'moneda' => [
					['moneda.chr_monecodigo','cargomantenimiento.chr_monecodigo']
				]
			],

			'expanded_relationships_from' => array (
  'moneda' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'moneda',
        1 => 'chr_monecodigo',
      ),
      1 => 
      array (
        0 => 'cargomantenimiento',
        1 => 'chr_monecodigo',
      ),
    ),
  ),
)
		];
	}	
}

