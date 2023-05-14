<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_DEBATESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_DEBATES',

			'id_name'		=> 'DEB_ID',

			'fields'		=> ['DEB_ID', 'USU_ID', 'DEB_FECHA_APERTURA', 'DEB_FECHA_CIERRE', 'DEB_TITULO', 'DEB_DESCRIPCION', 'DEB_PREGUNTA', 'DEB_BORRADO', 'DEB_HABILITADO', 'DEB_FILTROS', 'DEB_UNICA_RESPUESTA', 'DEB_TEMATICAS', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'DEB_ID' => 'INT',
				'USU_ID' => 'INT',
				'DEB_FECHA_APERTURA' => 'STR',
				'DEB_FECHA_CIERRE' => 'STR',
				'DEB_TITULO' => 'STR',
				'DEB_DESCRIPCION' => 'STR',
				'DEB_PREGUNTA' => 'STR',
				'DEB_BORRADO' => 'INT',
				'DEB_HABILITADO' => 'INT',
				'DEB_FILTROS' => 'STR',
				'DEB_UNICA_RESPUESTA' => 'STR',
				'DEB_TEMATICAS' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['DEB_ID'],

			'autoincrement' => 'DEB_ID',

			'nullable'		=> ['DEB_ID', 'DEB_TITULO', 'DEB_DESCRIPCION', 'DEB_PREGUNTA', 'DEB_BORRADO', 'DEB_HABILITADO', 'DEB_FILTROS', 'DEB_UNICA_RESPUESTA', 'DEB_TEMATICAS', 'created_at', 'updated_at'],

			'required'		=> ['USU_ID', 'DEB_FECHA_APERTURA', 'DEB_FECHA_CIERRE'],

			'uniques'		=> ['DEB_TITULO'],

			'rules' 		=> [
				'DEB_ID' => ['type' => 'int'],
				'USU_ID' => ['type' => 'int', 'required' => true],
				'DEB_FECHA_APERTURA' => ['type' => 'datetime', 'required' => true],
				'DEB_FECHA_CIERRE' => ['type' => 'datetime', 'required' => true],
				'DEB_TITULO' => ['type' => 'str', 'max' => 200],
				'DEB_DESCRIPCION' => ['type' => 'str'],
				'DEB_PREGUNTA' => ['type' => 'str'],
				'DEB_BORRADO' => ['type' => 'bool'],
				'DEB_HABILITADO' => ['type' => 'bool'],
				'DEB_FILTROS' => ['type' => 'str'],
				'DEB_UNICA_RESPUESTA' => ['type' => 'str', 'max' => 45],
				'DEB_TEMATICAS' => ['type' => 'str'],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime']
			],

			'fks' 			=> ['USU_ID'],

			'relationships' => [
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_DEBATES.USU_ID']
				],
				'TBL_OPCIONES_RESPUESTA_DEBATES' => [
					['TBL_OPCIONES_RESPUESTA_DEBATES.DEB_ID','TBL_DEBATES.DEB_ID']
				],
				'TBL_PARTICIPACION_DEBATE' => [
					['TBL_PARTICIPACION_DEBATE.DEB_ID','TBL_DEBATES.DEB_ID']
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
        0 => 'TBL_DEBATES',
        1 => 'USU_ID',
      ),
    ),
  ),
  'TBL_OPCIONES_RESPUESTA_DEBATES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_OPCIONES_RESPUESTA_DEBATES',
        1 => 'DEB_ID',
      ),
      1 => 
      array (
        0 => 'TBL_DEBATES',
        1 => 'DEB_ID',
      ),
    ),
  ),
  'TBL_PARTICIPACION_DEBATE' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PARTICIPACION_DEBATE',
        1 => 'DEB_ID',
      ),
      1 => 
      array (
        0 => 'TBL_DEBATES',
        1 => 'DEB_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_DEBATES.USU_ID']
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
        0 => 'TBL_DEBATES',
        1 => 'USU_ID',
      ),
    ),
  ),
)
		];
	}	
}

