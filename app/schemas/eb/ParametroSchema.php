<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class ParametroSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'parametro',

			'id_name'		=> 'chr_paracodigo',

			'attr_types'	=> [
				'chr_paracodigo' => 'STR',
				'vch_paradescripcion' => 'STR',
				'vch_paravalor' => 'STR',
				'vch_paraestado' => 'STR'
			],

			'primary'		=> ['chr_paracodigo'],

			'autoincrement' => null,

			'nullable'		=> ['vch_paraestado'],

			'uniques'		=> [],

			'rules' 		=> [
				'chr_paracodigo' => ['type' => 'str', 'required' => true],
				'vch_paradescripcion' => ['type' => 'str', 'max' => 50, 'required' => true],
				'vch_paravalor' => ['type' => 'str', 'max' => 70, 'required' => true],
				'vch_paraestado' => ['type' => 'str', 'max' => 15]
			],

			'fks' 			=> [],

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

