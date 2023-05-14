<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PROPUESTASSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PROPUESTAS',

			'id_name'		=> 'PRO_ID',

			'fields'		=> ['PRO_ID', 'PRO_TITULO', 'PRO_DESCRIPCION', 'PRO_FECHA_APERTURA', 'PRO_FECHA_CIERRE', 'PRO_BORRADO', 'USU_ID'],

			'attr_types'	=> [
				'PRO_ID' => 'INT',
				'PRO_TITULO' => 'STR',
				'PRO_DESCRIPCION' => 'STR',
				'PRO_FECHA_APERTURA' => 'STR',
				'PRO_FECHA_CIERRE' => 'STR',
				'PRO_BORRADO' => 'INT',
				'USU_ID' => 'INT'
			],

			'primary'		=> ['PRO_ID'],

			'autoincrement' => 'PRO_ID',

			'nullable'		=> ['PRO_ID', 'PRO_TITULO', 'PRO_DESCRIPCION', 'PRO_FECHA_APERTURA', 'PRO_FECHA_CIERRE', 'PRO_BORRADO', 'USU_ID'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'PRO_ID' => ['type' => 'int'],
				'PRO_TITULO' => ['type' => 'str', 'max' => 100],
				'PRO_DESCRIPCION' => ['type' => 'str'],
				'PRO_FECHA_APERTURA' => ['type' => 'datetime'],
				'PRO_FECHA_CIERRE' => ['type' => 'datetime'],
				'PRO_BORRADO' => ['type' => 'bool'],
				'USU_ID' => ['type' => 'int']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_PARTICIPACION_PROPUESTA' => [
					['TBL_PARTICIPACION_PROPUESTA.PRO_ID','TBL_PROPUESTAS.PRO_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_PARTICIPACION_PROPUESTA' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PARTICIPACION_PROPUESTA',
        1 => 'PRO_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PROPUESTAS',
        1 => 'PRO_ID',
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

