<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ClienteSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'cliente',

			'id_name'		=> 'chr_cliecodigo',

			'attr_types'	=> [
				'chr_cliecodigo' => 'STR',
				'vch_cliepaterno' => 'STR',
				'vch_cliematerno' => 'STR',
				'vch_clienombre' => 'STR',
				'chr_cliedni' => 'STR',
				'vch_clieciudad' => 'STR',
				'vch_cliedireccion' => 'STR',
				'vch_clietelefono' => 'STR',
				'vch_clieemail' => 'STR'
			],

			'primary'		=> ['chr_cliecodigo'],

			'autoincrement' => null,

			'nullable'		=> ['vch_clietelefono', 'vch_clieemail'],

			'uniques'		=> [],

			'rules' 		=> [
				'chr_cliecodigo' => ['type' => 'str', 'required' => true],
				'vch_cliepaterno' => ['type' => 'str', 'max' => 25, 'required' => true],
				'vch_cliematerno' => ['type' => 'str', 'max' => 25, 'required' => true],
				'vch_clienombre' => ['type' => 'str', 'max' => 30, 'required' => true],
				'chr_cliedni' => ['type' => 'str', 'required' => true],
				'vch_clieciudad' => ['type' => 'str', 'max' => 30, 'required' => true],
				'vch_cliedireccion' => ['type' => 'str', 'max' => 50, 'required' => true],
				'vch_clietelefono' => ['type' => 'str', 'max' => 20],
				'vch_clieemail' => ['type' => 'str', 'max' => 50]
			],

			'fks' 			=> [],

			'relationships' => [
				'cuenta' => [
					['cuenta.chr_cliecodigo','cliente.chr_cliecodigo']
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
        1 => 'chr_cliecodigo',
      ),
      1 => 
      array (
        0 => 'cliente',
        1 => 'chr_cliecodigo',
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

