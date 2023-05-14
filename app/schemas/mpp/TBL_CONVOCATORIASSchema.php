<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_CONVOCATORIASSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_CONVOCATORIAS',

			'id_name'		=> 'CON_ID',

			'fields'		=> ['CON_ID', 'CON_NOMBRE', 'CON_IMAGEN', 'CON_VIDEO', 'CON_OBJETIVO', 'CON_CRONOGRAMA', 'CON_DESCRIPCION', 'CON_FECHA_INICIO', 'CON_FECHA_FIN', 'CON_PUBLICO_CONVOCADO', 'CON_LINK', 'CON_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'CON_ID' => 'INT',
				'CON_NOMBRE' => 'STR',
				'CON_IMAGEN' => 'STR',
				'CON_VIDEO' => 'STR',
				'CON_OBJETIVO' => 'STR',
				'CON_CRONOGRAMA' => 'STR',
				'CON_DESCRIPCION' => 'STR',
				'CON_FECHA_INICIO' => 'STR',
				'CON_FECHA_FIN' => 'STR',
				'CON_PUBLICO_CONVOCADO' => 'STR',
				'CON_LINK' => 'STR',
				'CON_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['CON_ID'],

			'autoincrement' => 'CON_ID',

			'nullable'		=> ['CON_ID', 'CON_NOMBRE', 'CON_IMAGEN', 'CON_VIDEO', 'CON_OBJETIVO', 'CON_CRONOGRAMA', 'CON_DESCRIPCION', 'CON_FECHA_INICIO', 'CON_FECHA_FIN', 'CON_PUBLICO_CONVOCADO', 'CON_LINK', 'CON_BORRADO', 'created_at', 'updated_at'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'CON_ID' => ['type' => 'int'],
				'CON_NOMBRE' => ['type' => 'str', 'max' => 255],
				'CON_IMAGEN' => ['type' => 'str', 'max' => 255],
				'CON_VIDEO' => ['type' => 'str', 'max' => 255],
				'CON_OBJETIVO' => ['type' => 'str', 'max' => 100],
				'CON_CRONOGRAMA' => ['type' => 'str', 'max' => 255],
				'CON_DESCRIPCION' => ['type' => 'str'],
				'CON_FECHA_INICIO' => ['type' => 'datetime'],
				'CON_FECHA_FIN' => ['type' => 'datetime'],
				'CON_PUBLICO_CONVOCADO' => ['type' => 'str'],
				'CON_LINK' => ['type' => 'str', 'max' => 255],
				'CON_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_INSCRIPCIONES' => [
					['TBL_INSCRIPCIONES.CON_ID','TBL_CONVOCATORIAS.CON_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_INSCRIPCIONES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_INSCRIPCIONES',
        1 => 'CON_ID',
      ),
      1 => 
      array (
        0 => 'TBL_CONVOCATORIAS',
        1 => 'CON_ID',
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

