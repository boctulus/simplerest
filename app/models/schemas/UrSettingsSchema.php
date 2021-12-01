<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UrSettingsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'ur_settings',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'pref1' => 'INT',
				'pref2' => 'INT',
				'pref3' => 'INT',
				'ur_id' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => null,

			'nullable'		=> ['pref1', 'pref2', 'pref3'],

			'uniques'		=> ['ur_id'],

			'rules' 		=> [
				'id' => ['type' => 'int', 'required' => true],
				'pref1' => ['type' => 'bool'],
				'pref2' => ['type' => 'bool'],
				'pref3' => ['type' => 'bool'],
				'ur_id' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['ur_id'],

			'relationships' => [
				'ur' => [
					['ur.id_ur','ur_settings.ur_id']
				]
			],

			'expanded_relationships' => array (
				  'ur' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'ur',
				        1 => 'id_ur',
				      ),
				      1 => 
				      array (
				        0 => 'ur_settings',
				        1 => 'ur_id',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'ur' => [
					['ur.id_ur','ur_settings.ur_id']
				]
			],

			'expanded_relationships_from' => array (
				  'ur' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'ur',
				        1 => 'id_ur',
				      ),
				      1 => 
				      array (
				        0 => 'ur_settings',
				        1 => 'ur_id',
				      ),
				    ),
				  ),
				)
		];
	}	
}

