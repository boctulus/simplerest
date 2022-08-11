<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class OrgComunalEntidadRegistranteSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'org_comunal_entidad_registrante',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'tipo_vinculo', 'cant_personas', 'org_comunal_id', 'entidad_registrante_id', 'deleted_at', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'tipo_vinculo' => 'STR',
				'cant_personas' => 'INT',
				'org_comunal_id' => 'INT',
				'entidad_registrante_id' => 'INT',
				'deleted_at' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'deleted_at', 'created_at', 'updated_at'],

			'required'		=> ['tipo_vinculo', 'cant_personas', 'org_comunal_id', 'entidad_registrante_id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'tipo_vinculo' => ['type' => 'str', 'max' => 255, 'required' => true],
				'cant_personas' => ['type' => 'int', 'required' => true],
				'org_comunal_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'entidad_registrante_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'deleted_at' => ['type' => 'timestamp'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> ['entidad_registrante_id', 'org_comunal_id'],

			'relationships' => [
				'entidades_registrantes' => [
					['entidades_registrantes.id','org_comunal_entidad_registrante.entidad_registrante_id']
				],
				'org_comunales' => [
					['org_comunales.id','org_comunal_entidad_registrante.org_comunal_id']
				]
			],

			'expanded_relationships' => array (
  'entidades_registrantes' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'entidades_registrantes',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunal_entidad_registrante',
        1 => 'entidad_registrante_id',
      ),
    ),
  ),
  'org_comunales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'org_comunales',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunal_entidad_registrante',
        1 => 'org_comunal_id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'entidades_registrantes' => [
					['entidades_registrantes.id','org_comunal_entidad_registrante.entidad_registrante_id']
				],
				'org_comunales' => [
					['org_comunales.id','org_comunal_entidad_registrante.org_comunal_id']
				]
			],

			'expanded_relationships_from' => array (
  'entidades_registrantes' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'entidades_registrantes',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunal_entidad_registrante',
        1 => 'entidad_registrante_id',
      ),
    ),
  ),
  'org_comunales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'org_comunales',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunal_entidad_registrante',
        1 => 'org_comunal_id',
      ),
    ),
  ),
)
		];
	}	
}

