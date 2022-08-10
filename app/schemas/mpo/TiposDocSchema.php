<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TiposDocSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tipos_doc',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'nombre', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'nombre' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'created_at', 'updated_at'],

			'required'		=> ['nombre'],

			'uniques'		=> ['nombre'],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'nombre' => ['type' => 'str', 'max' => 40, 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'representantes_legales' => [
					['representantes_legales.tipo_doc_id','tipos_doc.id']
				]
			],

			'expanded_relationships' => array (
  'representantes_legales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'representantes_legales',
        1 => 'tipo_doc_id',
      ),
      1 => 
      array (
        0 => 'tipos_doc',
        1 => 'id',
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

