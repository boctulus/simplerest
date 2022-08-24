<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PROYECTOS_EJECUTADOS_COOPERACIONSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PROYECTOS_EJECUTADOS_COOPERACION',

			'id_name'		=> 'ID_PCO',

			'fields'		=> ['ID_PCO', 'PCO_ANNO', 'PCO_DURACION', 'PCO_VALOR', 'PCO_ENTIDAD', 'PCO_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_PCO' => 'INT',
				'PCO_ANNO' => 'INT',
				'PCO_DURACION' => 'STR',
				'PCO_VALOR' => 'INT',
				'PCO_ENTIDAD' => 'STR',
				'PCO_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_PCO'],

			'autoincrement' => 'ID_PCO',

			'nullable'		=> ['ID_PCO', 'PCO_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['PCO_ANNO', 'PCO_DURACION', 'PCO_VALOR', 'PCO_ENTIDAD'],

			'uniques'		=> [],

			'rules' 		=> [
				'ID_PCO' => ['type' => 'int', 'min' => 0],
				'PCO_ANNO' => ['type' => 'int', 'required' => true],
				'PCO_DURACION' => ['type' => 'str', 'max' => 30, 'required' => true],
				'PCO_VALOR' => ['type' => 'int', 'required' => true],
				'PCO_ENTIDAD' => ['type' => 'str', 'max' => 60, 'required' => true],
				'PCO_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_ORG_COMUNALES' => [
					['TBL_ORG_COMUNALES.PR_EJ_COOP_ID','TBL_PROYECTOS_EJECUTADOS_COOPERACION.ID_PCO']
				]
			],

			'expanded_relationships' => array (
  'TBL_ORG_COMUNALES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ORG_COMUNALES',
        1 => 'PR_EJ_COOP_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PROYECTOS_EJECUTADOS_COOPERACION',
        1 => 'ID_PCO',
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

