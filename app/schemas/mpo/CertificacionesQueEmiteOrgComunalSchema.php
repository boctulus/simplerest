<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CertificacionesQueEmiteOrgComunalSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'certificaciones_que_emite_org_comunal',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'nombre', 'deleted_at', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'nombre' => 'STR',
				'deleted_at' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'deleted_at', 'created_at', 'updated_at'],

			'required'		=> ['nombre'],

			'uniques'		=> ['nombre'],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'nombre' => ['type' => 'str', 'max' => 60, 'required' => true],
				'deleted_at' => ['type' => 'timestamp'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'org_comunales' => [
					['org_comunales.certificacion_que_emite_org_comunal_id','certificaciones_que_emite_org_comunal.id']
				]
			],

			'expanded_relationships' => array (
  'org_comunales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'org_comunales',
        1 => 'certificacion_que_emite_org_comunal_id',
      ),
      1 => 
      array (
        0 => 'certificaciones_que_emite_org_comunal',
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

