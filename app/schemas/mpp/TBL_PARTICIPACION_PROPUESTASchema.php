<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PARTICIPACION_PROPUESTASchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PARTICIPACION_PROPUESTA',

			'id_name'		=> 'PAR_ID_RADICADO',

			'fields'		=> ['PAR_ID_RADICADO', 'USU_ID', 'PRO_ID', 'PAR_COMENTARIO', 'PAR_FECHA_HORA', 'PAR_BORRADO', 'PAR_LIMITE_PARTICIPACION', 'PAR_DOCUMENTO'],

			'attr_types'	=> [
				'PAR_ID_RADICADO' => 'INT',
				'USU_ID' => 'INT',
				'PRO_ID' => 'INT',
				'PAR_COMENTARIO' => 'STR',
				'PAR_FECHA_HORA' => 'STR',
				'PAR_BORRADO' => 'INT',
				'PAR_LIMITE_PARTICIPACION' => 'INT',
				'PAR_DOCUMENTO' => 'STR'
			],

			'primary'		=> ['PAR_ID_RADICADO', 'USU_ID', 'PRO_ID'],

			'autoincrement' => 'PAR_ID_RADICADO',

			'nullable'		=> ['PAR_ID_RADICADO', 'PAR_BORRADO', 'PAR_LIMITE_PARTICIPACION', 'PAR_DOCUMENTO'],

			'required'		=> ['USU_ID', 'PRO_ID', 'PAR_COMENTARIO', 'PAR_FECHA_HORA'],

			'uniques'		=> [],

			'rules' 		=> [
				'PAR_ID_RADICADO' => ['type' => 'int'],
				'USU_ID' => ['type' => 'int', 'required' => true],
				'PRO_ID' => ['type' => 'int', 'required' => true],
				'PAR_COMENTARIO' => ['type' => 'str', 'required' => true],
				'PAR_FECHA_HORA' => ['type' => 'datetime', 'required' => true],
				'PAR_BORRADO' => ['type' => 'bool'],
				'PAR_LIMITE_PARTICIPACION' => ['type' => 'int'],
				'PAR_DOCUMENTO' => ['type' => 'str', 'max' => 45]
			],

			'fks' 			=> ['PRO_ID', 'USU_ID'],

			'relationships' => [
				'TBL_PROPUESTAS' => [
					['TBL_PROPUESTAS.PRO_ID','TBL_PARTICIPACION_PROPUESTA.PRO_ID']
				],
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_PARTICIPACION_PROPUESTA.USU_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_PROPUESTAS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PROPUESTAS',
        1 => 'PRO_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PARTICIPACION_PROPUESTA',
        1 => 'PRO_ID',
      ),
    ),
  ),
  'TBL_USUARIOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PARTICIPACION_PROPUESTA',
        1 => 'USU_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_PROPUESTAS' => [
					['TBL_PROPUESTAS.PRO_ID','TBL_PARTICIPACION_PROPUESTA.PRO_ID']
				],
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_PARTICIPACION_PROPUESTA.USU_ID']
				]
			],

			'expanded_relationships_from' => array (
  'TBL_PROPUESTAS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PROPUESTAS',
        1 => 'PRO_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PARTICIPACION_PROPUESTA',
        1 => 'PRO_ID',
      ),
    ),
  ),
  'TBL_USUARIOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PARTICIPACION_PROPUESTA',
        1 => 'USU_ID',
      ),
    ),
  ),
)
		];
	}	
}

