<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_SONDEOSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_SONDEOS',

			'id_name'		=> 'SON_ID',

			'fields'		=> ['SON_ID', 'SON_TITULO', 'SON_PREGUNTA', 'SON_FECHA_APERTURA', 'SON_FECHA_CIERRE', 'SON_BORRADO', 'SON_HABILITADO', 'SON_FILTRO', 'USU_ID', 'SON_UNICA_RESPUESTA', 'SON_TEMATICAS', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'SON_ID' => 'INT',
				'SON_TITULO' => 'STR',
				'SON_PREGUNTA' => 'STR',
				'SON_FECHA_APERTURA' => 'STR',
				'SON_FECHA_CIERRE' => 'STR',
				'SON_BORRADO' => 'INT',
				'SON_HABILITADO' => 'INT',
				'SON_FILTRO' => 'STR',
				'USU_ID' => 'INT',
				'SON_UNICA_RESPUESTA' => 'STR',
				'SON_TEMATICAS' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['SON_ID'],

			'autoincrement' => 'SON_ID',

			'nullable'		=> ['SON_ID', 'SON_BORRADO', 'SON_HABILITADO', 'SON_FILTRO', 'SON_UNICA_RESPUESTA', 'created_at', 'updated_at'],

			'required'		=> ['SON_TITULO', 'SON_PREGUNTA', 'SON_FECHA_APERTURA', 'SON_FECHA_CIERRE', 'USU_ID', 'SON_TEMATICAS'],

			'uniques'		=> ['SON_TITULO'],

			'rules' 		=> [
				'SON_ID' => ['type' => 'int'],
				'SON_TITULO' => ['type' => 'str', 'max' => 200, 'required' => true],
				'SON_PREGUNTA' => ['type' => 'str', 'required' => true],
				'SON_FECHA_APERTURA' => ['type' => 'datetime', 'required' => true],
				'SON_FECHA_CIERRE' => ['type' => 'datetime', 'required' => true],
				'SON_BORRADO' => ['type' => 'bool'],
				'SON_HABILITADO' => ['type' => 'bool'],
				'SON_FILTRO' => ['type' => 'str'],
				'USU_ID' => ['type' => 'int', 'required' => true],
				'SON_UNICA_RESPUESTA' => ['type' => 'str'],
				'SON_TEMATICAS' => ['type' => 'str', 'max' => 100, 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> ['USU_ID'],

			'relationships' => [
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_SONDEOS.USU_ID']
				],
				'TBL_OPCIONES_RESPUESTA_SONDEO' => [
					['TBL_OPCIONES_RESPUESTA_SONDEO.SON_ID','TBL_SONDEOS.SON_ID']
				],
				'TBL_PARTICIPACION_SONDEO' => [
					['TBL_PARTICIPACION_SONDEO.SON_ID','TBL_SONDEOS.SON_ID']
				],
				'TBL_PREGUNTA_SONDEO' => [
					['TBL_PREGUNTA_SONDEO.SON_ID','TBL_SONDEOS.SON_ID']
				]
			],

			'expanded_relationships' => array (
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
        0 => 'TBL_SONDEOS',
        1 => 'USU_ID',
      ),
    ),
  ),
  'TBL_OPCIONES_RESPUESTA_SONDEO' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_OPCIONES_RESPUESTA_SONDEO',
        1 => 'SON_ID',
      ),
      1 => 
      array (
        0 => 'TBL_SONDEOS',
        1 => 'SON_ID',
      ),
    ),
  ),
  'TBL_PARTICIPACION_SONDEO' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PARTICIPACION_SONDEO',
        1 => 'SON_ID',
      ),
      1 => 
      array (
        0 => 'TBL_SONDEOS',
        1 => 'SON_ID',
      ),
    ),
  ),
  'TBL_PREGUNTA_SONDEO' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PREGUNTA_SONDEO',
        1 => 'SON_ID',
      ),
      1 => 
      array (
        0 => 'TBL_SONDEOS',
        1 => 'SON_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_SONDEOS.USU_ID']
				]
			],

			'expanded_relationships_from' => array (
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
        0 => 'TBL_SONDEOS',
        1 => 'USU_ID',
      ),
    ),
  ),
)
		];
	}	
}

