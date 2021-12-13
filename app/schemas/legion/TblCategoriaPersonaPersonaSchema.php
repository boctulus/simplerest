<?php

namespace simplerest\schemas\legion;

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

			'primary'		=> ['cpp_intId'],

			'autoincrement' => 'cpp_intId',

			'nullable'		=> ['cpp_intId', 'cat_dtimFechaCreacion'],

			'uniques'		=> [],

			'rules' 		=> [
				'cpp_intId' => ['type' => 'int'],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'cap_intIdCategoriaPersona' => ['type' => 'int', 'required' => true],
				'cat_dtimFechaCreacion' => ['type' => 'datetime']
			],

			'fks' 			=> ['cap_intIdCategoriaPersona', 'per_intIdPersona'],

			'relationships' => [
				'tbl_categoria_persona' => [
					['tbl_categoria_persona.cap_intId','tbl_categoria_persona_persona.cap_intIdCategoriaPersona']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_categoria_persona_persona.per_intIdPersona']
				]
			],

			'expanded_relationships' => array (
				  'tbl_categoria_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_persona',
				        1 => 'cap_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_categoria_persona_persona',
				        1 => 'cap_intIdCategoriaPersona',
				      ),
				    ),
				  ),
				  'tbl_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_categoria_persona_persona',
				        1 => 'per_intIdPersona',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_categoria_persona' => [
					['tbl_categoria_persona.cap_intId','tbl_categoria_persona_persona.cap_intIdCategoriaPersona']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_categoria_persona_persona.per_intIdPersona']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_categoria_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_persona',
				        1 => 'cap_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_categoria_persona_persona',
				        1 => 'cap_intIdCategoriaPersona',
				      ),
				    ),
				  ),
				  'tbl_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_categoria_persona_persona',
				        1 => 'per_intIdPersona',
				      ),
				    ),
				  ),
				)
		];
	}	
}

