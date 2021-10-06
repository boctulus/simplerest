<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class XxySchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'xxy',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'otro' => 'STR',
				'otro2' => 'INT'
			],

			'nullable'		=> ['id'],

			'rules' 		=> [
				'id' => ['type' => 'int'],
				'otro' => ['type' => 'str', 'required' => true],
				'otro2' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				
			]
		];
	}	
}

