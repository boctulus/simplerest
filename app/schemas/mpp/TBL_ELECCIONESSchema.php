<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_ELECCIONESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_ELECCIONES',

			'id_name'		=> 'ELE_ID',

			'fields'		=> ['ELE_ID', 'ELE_TITULO', 'ELE_PREGUNTA', 'ELE_FECHA_APERTURA', 'ELE_FECHA_CIERRE', 'ELE_BORRADO', 'ELE_HABILITADO', 'ELE_FILTRO', 'USU_ID', 'TEM_ID'],

			'attr_types'	=> [
				'ELE_ID' => 'INT',
				'ELE_TITULO' => 'STR',
				'ELE_PREGUNTA' => 'STR',
				'ELE_FECHA_APERTURA' => 'STR',
				'ELE_FECHA_CIERRE' => 'STR',
				'ELE_BORRADO' => 'INT',
				'ELE_HABILITADO' => 'INT',
				'ELE_FILTRO' => 'STR',
				'USU_ID' => 'INT',
				'TEM_ID' => 'INT'
			],

			'primary'		=> ['ELE_ID'],

			'autoincrement' => 'ELE_ID',

			'nullable'		=> ['ELE_ID', 'ELE_TITULO', 'ELE_PREGUNTA', 'ELE_FECHA_APERTURA', 'ELE_FECHA_CIERRE', 'ELE_BORRADO', 'ELE_HABILITADO', 'ELE_FILTRO', 'USU_ID', 'TEM_ID'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'ELE_ID' => ['type' => 'int'],
				'ELE_TITULO' => ['type' => 'str', 'max' => 200],
				'ELE_PREGUNTA' => ['type' => 'str', 'max' => 200],
				'ELE_FECHA_APERTURA' => ['type' => 'datetime'],
				'ELE_FECHA_CIERRE' => ['type' => 'datetime'],
				'ELE_BORRADO' => ['type' => 'bool'],
				'ELE_HABILITADO' => ['type' => 'bool'],
				'ELE_FILTRO' => ['type' => 'str'],
				'USU_ID' => ['type' => 'int'],
				'TEM_ID' => ['type' => 'int']
			],

			'fks' 			=> ['TEM_ID', 'USU_ID'],

			'relationships' => [
				'TBL_TEMATICAS' => [
					['TBL_TEMATICAS.TEM_ID','TBL_ELECCIONES.TEM_ID']
				],
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_ELECCIONES.USU_ID']
				],
				'TBL_PARTICIPACION_ELECCION' => [
					['TBL_PARTICIPACION_ELECCION.ELE_ID','TBL_ELECCIONES.ELE_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_TEMATICAS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_TEMATICAS',
        1 => 'TEM_ID',
      ),
      1 => 
      array (
        0 => 'TBL_ELECCIONES',
        1 => 'TEM_ID',
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
        0 => 'TBL_ELECCIONES',
        1 => 'USU_ID',
      ),
    ),
  ),
  'TBL_PARTICIPACION_ELECCION' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PARTICIPACION_ELECCION',
        1 => 'ELE_ID',
      ),
      1 => 
      array (
        0 => 'TBL_ELECCIONES',
        1 => 'ELE_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_TEMATICAS' => [
					['TBL_TEMATICAS.TEM_ID','TBL_ELECCIONES.TEM_ID']
				],
				'TBL_USUARIOS' => [
					['TBL_USUARIOS.USU_ID','TBL_ELECCIONES.USU_ID']
				]
			],

			'expanded_relationships_from' => array (
  'TBL_TEMATICAS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_TEMATICAS',
        1 => 'TEM_ID',
      ),
      1 => 
      array (
        0 => 'TBL_ELECCIONES',
        1 => 'TEM_ID',
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
        0 => 'TBL_ELECCIONES',
        1 => 'USU_ID',
      ),
    ),
  ),
)
		];
	}	
}

