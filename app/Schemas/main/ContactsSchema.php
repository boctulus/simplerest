<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ContactsSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'Contacts',

			'id_name'			=> 'id',

			'fields'			=> ['id', 'full_name', 'company', 'website', 'job_title', 'phone_number_1', 'phone_number_2', 'notes', 'favorite', 'created_at', 'updated_at'],

			'attr_types'		=> [
				'id' => 'INT',
				'full_name' => 'STR',
				'company' => 'STR',
				'website' => 'STR',
				'job_title' => 'STR',
				'phone_number_1' => 'STR',
				'phone_number_2' => 'STR',
				'notes' => 'STR',
				'favorite' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['id'],

			'autoincrement' 	=> 'id',

			'nullable'			=> ['id', 'company', 'website', 'job_title', 'phone_number_1', 'phone_number_2', 'notes', 'updated_at'],

			'required'			=> ['full_name', 'favorite', 'created_at'],

			'uniques'			=> [],

			'rules' 			=> [
				'id' => ['type' => 'int'],
				'full_name' => ['type' => 'str', 'max' => 150, 'required' => true],
				'company' => ['type' => 'str', 'max' => 100],
				'website' => ['type' => 'str', 'max' => 200],
				'job_title' => ['type' => 'str', 'max' => 100],
				'phone_number_1' => ['type' => 'str', 'max' => 20],
				'phone_number_2' => ['type' => 'str', 'max' => 20],
				'notes' => ['type' => 'str'],
				'favorite' => ['type' => 'bool', 'required' => true],
				'created_at' => ['type' => 'datetime', 'required' => true],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 				=> [],

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

