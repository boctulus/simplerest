<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCategoriaPersonaPersonaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_categoria_persona_persona',

			'id_name'		=> 'cpp_intId',

			'attr_types'	=> [
				'cpp_intId' => 'INT',
				'per_intIdPersona' => 'INT',
				'cap_intIdCategoriaPersona' => 'INT',
				'cat_dtimFechaCreacion' => 'STR'
			],

			'nullable'		=> ['cpp_intId', 'cat_dtimFechaCreacion'],

			'rules' 		=> [
				'cpp_intId' => ['type' => 'int'],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'cap_intIdCategoriaPersona' => ['type' => 'int', 'required' => true],
				'cat_dtimFechaCreacion' => ['type' => 'datetime']
			],

			'relationships' => [
				
			]
		];
	}	
}
