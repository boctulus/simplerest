<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PARTICIPACION_DEBATESchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PARTICIPACION_DEBATE',

			'id_name'		=> 'PAR_ID_RADICADO',

			'fields'		=> ['PAR_ID_RADICADO', 'USU_ID', 'DEB_ID', 'PAR_COMENTARIO', 'PAR_FECHA_HORA', 'PAR_BORRADO', 'PAR_LIMITE_PARTICIPACION', 'PAR_DOCUMENTO'],

			'attr_types'	=> [
				'PAR_ID_RADICADO' => 'INT',
				'USU_ID' => 'INT',
				'DEB_ID' => 'INT',
				'PAR_COMENTARIO' => 'STR',
				'PAR_FECHA_HORA' => 'STR',
				'PAR_BORRADO' => 'INT',
				'PAR_LIMITE_PARTICIPACION' => 'INT',
				'PAR_DOCUMENTO' => 'STR'
			],

			'primary'		=> ['PAR_ID_RADICADO'],

			'autoincrement' => 'PAR_ID_RADICADO',

			'nullable'		=> ['PAR_ID_RADICADO', 'PAR_BORRADO', 'PAR_LIMITE_PARTICIPACION'],

			'required'		=> ['USU_ID', 'DEB_ID', 'PAR_COMENTARIO', 'PAR_FECHA_HORA', 'PAR_DOCUMENTO'],

			'uniques'		=> [],

			'rules' 		=> [
				'PAR_ID_RADICADO' => ['type' => 'int'],
				'USU_ID' => ['type' => 'int', 'required' => true],
				'DEB_ID' => ['type' => 'int', 'required' => true],
				'PAR_COMENTARIO' => ['type' => 'str', 'required' => true],
				'PAR_FECHA_HORA' => ['type' => 'datetime', 'required' => true],
				'PAR_BORRADO' => ['type' => 'bool'],
				'PAR_LIMITE_PARTICIPACION' => ['type' => 'int'],
				'PAR_DOCUMENTO' => ['type' => 'str', 'max' => 45, 'required' => true]
			],

			'fks' 			=> ['DEB_ID', 'USU_ID'],

			'relationships' => [
				'TBL_DEBATES' => [
					['TBL_DEBATES.DEB_ID','TBL_PARTICIPACION_DEBATE.DEB_ID']
				],
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_PARTICIPACION_DEBATE.USU_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_DEBATES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_DEBATES',
        1 => 'DEB_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PARTICIPACION_DEBATE',
        1 => 'DEB_ID',
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
        0 => 'TBL_PARTICIPACION_DEBATE',
        1 => 'USU_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_DEBATES' => [
					['TBL_DEBATES.DEB_ID','TBL_PARTICIPACION_DEBATE.DEB_ID']
				],
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_PARTICIPACION_DEBATE.USU_ID']
				]
			],

			'expanded_relationships_from' => array (
  'TBL_DEBATES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_DEBATES',
        1 => 'DEB_ID',
      ),
      1 => 
      array (
        0 => 'TBL_PARTICIPACION_DEBATE',
        1 => 'DEB_ID',
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
        0 => 'TBL_PARTICIPACION_DEBATE',
        1 => 'USU_ID',
      ),
    ),
  ),
)
		];
	}	
}

