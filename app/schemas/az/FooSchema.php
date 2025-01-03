<?php

namespace simplerest\schemas\az;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class FooSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'foo',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'bar', 'hide', 'alta', 'creado_por', 'fecha_modificacion', 'modificado_por', 'fecha_borrado', 'borrado_por'],

			'attr_types'	=> [
				'id' => 'INT',
				'bar' => 'STR',
				'hide' => 'INT',
				'alta' => 'STR',
				'creado_por' => 'INT',
				'fecha_modificacion' => 'STR',
				'modificado_por' => 'INT',
				'fecha_borrado' => 'STR',
				'borrado_por' => 'INT'
			],

			'primary'		=> ['id'],

			'autoincrement' => null,

			'nullable'		=> ['hide', 'alta', 'creado_por', 'fecha_modificacion', 'modificado_por', 'fecha_borrado', 'borrado_por'],

			'required'		=> ['id', 'bar'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'required' => true],
				'bar' => ['type' => 'str', 'max' => 45, 'required' => true],
				'hide' => ['type' => 'bool'],
				'alta' => ['type' => 'datetime'],
				'creado_por' => ['type' => 'int'],
				'fecha_modificacion' => ['type' => 'datetime'],
				'modificado_por' => ['type' => 'int'],
				'fecha_borrado' => ['type' => 'datetime'],
				'borrado_por' => ['type' => 'int']
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

