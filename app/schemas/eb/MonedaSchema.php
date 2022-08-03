<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class MonedaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'moneda',

			'id_name'		=> 'chr_monecodigo',

			'attr_types'	=> [
				'chr_monecodigo' => 'STR',
				'vch_monedescripcion' => 'STR'
			],

			'primary'		=> ['chr_monecodigo'],

			'autoincrement' => null,

			'nullable'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'chr_monecodigo' => ['type' => 'str', 'required' => true],
				'vch_monedescripcion' => ['type' => 'str', 'max' => 20, 'required' => true]
			],

			'fks' 			=> [],

			'relationships' => [
				'cargomantenimiento' => [
					['cargomantenimiento.chr_monecodigo','moneda.chr_monecodigo']
				],
				'costomovimiento' => [
					['costomovimiento.chr_monecodigo','moneda.chr_monecodigo']
				],
				'cuenta' => [
					['cuenta.chr_monecodigo','moneda.chr_monecodigo']
				],
				'interesmensual' => [
					['interesmensual.chr_monecodigo','moneda.chr_monecodigo']
				]
			],

			'expanded_relationships' => array (
  'cargomantenimiento' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'cargomantenimiento',
        1 => 'chr_monecodigo',
      ),
      1 => 
      array (
        0 => 'moneda',
        1 => 'chr_monecodigo',
      ),
    ),
  ),
  'costomovimiento' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'costomovimiento',
        1 => 'chr_monecodigo',
      ),
      1 => 
      array (
        0 => 'moneda',
        1 => 'chr_monecodigo',
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
        1 => 'chr_monecodigo',
      ),
      1 => 
      array (
        0 => 'moneda',
        1 => 'chr_monecodigo',
      ),
    ),
  ),
  'interesmensual' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'interesmensual',
        1 => 'chr_monecodigo',
      ),
      1 => 
      array (
        0 => 'moneda',
        1 => 'chr_monecodigo',
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

