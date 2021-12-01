<?php

namespace simplerest\models\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class USettingsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'u_settings',

			'id_name'		=> 'id_us',

			'attr_types'	=> [
				'id_us' => 'INT',
				'pref1' => 'INT',
				'pref2' => 'INT',
				'pref3' => 'INT'
			],

			'primary'		=> ['id_us'],

			'autoincrement' => 'id_us',

			'nullable'		=> ['id_us', 'pref1', 'pref2', 'pref3'],

			'uniques'		=> [],

			'rules' 		=> [
				'id_us' => ['type' => 'int'],
				'pref1' => ['type' => 'bool'],
				'pref2' => ['type' => 'bool'],
				'pref3' => ['type' => 'bool']
			],

			'fks' 			=> [],

			'relationships' => [
				'u' => [
					['u.u_settings_id','u_settings.id_us']
				]
			],

			'expanded_relationships' => array (
				  'u' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'u',
				        1 => 'u_settings_id',
				      ),
				      1 => 
				      array (
				        0 => 'u_settings',
				        1 => 'id_us',
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

