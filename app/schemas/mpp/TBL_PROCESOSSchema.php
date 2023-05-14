<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_PROCESOSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_PROCESOS',

			'id_name'		=> 'PRO_ID',

			'fields'		=> ['PRO_ID', 'PRO_TITULO', 'PRO_PREGUNTA', 'PRO_FECHA_APERTURA', 'PRO_FECHA_CIERRE', 'PRO_BORRADO', 'PRO_TIPO', 'PRO_HABILITADO', 'PRO_FILTRO', 'PRO_DOCUMENTO', 'USU_ID', 'PRO_TEMATICAS', 'PRO_UNICA_RESPUESTA'],

			'attr_types'	=> [
				'PRO_ID' => 'INT',
				'PRO_TITULO' => 'STR',
				'PRO_PREGUNTA' => 'STR',
				'PRO_FECHA_APERTURA' => 'STR',
				'PRO_FECHA_CIERRE' => 'STR',
				'PRO_BORRADO' => 'INT',
				'PRO_TIPO' => 'STR',
				'PRO_HABILITADO' => 'INT',
				'PRO_FILTRO' => 'STR',
				'PRO_DOCUMENTO' => 'STR',
				'USU_ID' => 'INT',
				'PRO_TEMATICAS' => 'STR',
				'PRO_UNICA_RESPUESTA' => 'STR'
			],

			'primary'		=> ['PRO_ID'],

			'autoincrement' => 'PRO_ID',

			'nullable'		=> ['PRO_ID', 'PRO_TITULO', 'PRO_PREGUNTA', 'PRO_FECHA_APERTURA', 'PRO_FECHA_CIERRE', 'PRO_BORRADO', 'PRO_HABILITADO', 'PRO_FILTRO', 'PRO_DOCUMENTO', 'USU_ID', 'PRO_TEMATICAS', 'PRO_UNICA_RESPUESTA'],

			'required'		=> ['PRO_TIPO'],

			'uniques'		=> ['PRO_TITULO'],

			'rules' 		=> [
				'PRO_ID' => ['type' => 'int'],
				'PRO_TITULO' => ['type' => 'str', 'max' => 45],
				'PRO_PREGUNTA' => ['type' => 'str', 'max' => 45],
				'PRO_FECHA_APERTURA' => ['type' => 'datetime'],
				'PRO_FECHA_CIERRE' => ['type' => 'datetime'],
				'PRO_BORRADO' => ['type' => 'bool'],
				'PRO_TIPO' => ['type' => 'str', 'max' => 45, 'required' => true],
				'PRO_HABILITADO' => ['type' => 'bool'],
				'PRO_FILTRO' => ['type' => 'str'],
				'PRO_DOCUMENTO' => ['type' => 'str', 'max' => 255],
				'USU_ID' => ['type' => 'int'],
				'PRO_TEMATICAS' => ['type' => 'str'],
				'PRO_UNICA_RESPUESTA' => ['type' => 'str', 'max' => 45]
			],

			'fks' 			=> ['USU_ID'],

			'relationships' => [
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_PROCESOS.USU_ID']
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
        0 => 'TBL_PROCESOS',
        1 => 'USU_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_PROCESOS.USU_ID']
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
        0 => 'TBL_PROCESOS',
        1 => 'USU_ID',
      ),
    ),
  ),
)
		];
	}	
}

