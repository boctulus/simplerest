<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TipomovimientoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tipomovimiento',

			'id_name'		=> 'chr_tipocodigo',

			'attr_types'	=> [
				'chr_tipocodigo' => 'STR',
				'vch_tipodescripcion' => 'STR',
				'vch_tipoaccion' => 'STR',
				'vch_tipoestado' => 'STR'
			],

			'primary'		=> ['chr_tipocodigo'],

			'autoincrement' => null,

			'nullable'		=> ['vch_tipoestado'],

			'uniques'		=> [],

			'rules' 		=> [
				'chr_tipocodigo' => ['type' => 'str', 'required' => true],
				'vch_tipodescripcion' => ['type' => 'str', 'max' => 40, 'required' => true],
				'vch_tipoaccion' => ['type' => 'str', 'max' => 10, 'required' => true],
				'vch_tipoestado' => ['type' => 'str', 'max' => 15]
			],

			'fks' 			=> [],

			'relationships' => [
				'movimiento' => [
					['movimiento.chr_tipocodigo','tipomovimiento.chr_tipocodigo']
				]
			],

			'expanded_relationships' => array (
  'movimiento' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'movimiento',
        1 => 'chr_tipocodigo',
      ),
      1 => 
      array (
        0 => 'tipomovimiento',
        1 => 'chr_tipocodigo',
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

