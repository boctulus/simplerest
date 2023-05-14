<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_USUARIOSSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_USUARIOS',

			'id_name'		=> 'USU_ID',

			'fields'		=> ['USU_ID', 'USU_TIPO_DOCUMENTO', 'USU_DOCUMENTO', 'USU_CONTRASENA', 'USU_MUNICIPIO_EXPEDICION_DOCUMENTO', 'USU_NOMBRES', 'USU_APELLIDOS', 'USU_DIRECCION', 'USU_LUGAR_RESIDENCIA', 'USU_CELULAR', 'USU_TELEFONO', 'USU_EMAIL', 'USU_GENERO', 'ASO_ID', 'USU_PARTICIPO_PL', 'USU_PARTICIPO_PP', 'USU_FECHA_NACIMIENTO', 'USU_FECHA_EXPEDICION', 'USU_FECHA_ACEPTACION_TERMINOS', 'USU_FECHA_REGISTRO', 'BAR_ID', 'USU_ESTADO', 'USU_PAIS', 'USU_VALIDADO_REGISTRADURIA', 'USU_BORRADO', 'USU_FOTO', 'MUN_ID', 'ROL_ID', 'DEP_ID', 'created_at', 'updated_at', 'email_verified_at', 'USU_CODIGOCONFIRMACION', 'COM_ID'],

			'attr_types'	=> [
				'USU_ID' => 'INT',
				'USU_TIPO_DOCUMENTO' => 'STR',
				'USU_DOCUMENTO' => 'STR',
				'USU_CONTRASENA' => 'STR',
				'USU_MUNICIPIO_EXPEDICION_DOCUMENTO' => 'INT',
				'USU_NOMBRES' => 'STR',
				'USU_APELLIDOS' => 'STR',
				'USU_DIRECCION' => 'STR',
				'USU_LUGAR_RESIDENCIA' => 'STR',
				'USU_CELULAR' => 'STR',
				'USU_TELEFONO' => 'STR',
				'USU_EMAIL' => 'STR',
				'USU_GENERO' => 'STR',
				'ASO_ID' => 'INT',
				'USU_PARTICIPO_PL' => 'INT',
				'USU_PARTICIPO_PP' => 'INT',
				'USU_FECHA_NACIMIENTO' => 'STR',
				'USU_FECHA_EXPEDICION' => 'STR',
				'USU_FECHA_ACEPTACION_TERMINOS' => 'STR',
				'USU_FECHA_REGISTRO' => 'STR',
				'BAR_ID' => 'INT',
				'USU_ESTADO' => 'STR',
				'USU_PAIS' => 'STR',
				'USU_VALIDADO_REGISTRADURIA' => 'INT',
				'USU_BORRADO' => 'INT',
				'USU_FOTO' => 'STR',
				'MUN_ID' => 'INT',
				'ROL_ID' => 'INT',
				'DEP_ID' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'email_verified_at' => 'INT',
				'USU_CODIGOCONFIRMACION' => 'STR',
				'COM_ID' => 'INT'
			],

			'primary'		=> ['USU_ID'],

			'autoincrement' => 'USU_ID',

			'nullable'		=> ['USU_ID', 'USU_CONTRASENA', 'USU_MUNICIPIO_EXPEDICION_DOCUMENTO', 'USU_DIRECCION', 'USU_LUGAR_RESIDENCIA', 'USU_CELULAR', 'USU_TELEFONO', 'USU_EMAIL', 'USU_GENERO', 'ASO_ID', 'USU_PARTICIPO_PL', 'USU_PARTICIPO_PP', 'USU_FECHA_NACIMIENTO', 'USU_FECHA_EXPEDICION', 'USU_FECHA_ACEPTACION_TERMINOS', 'USU_FECHA_REGISTRO', 'BAR_ID', 'USU_ESTADO', 'USU_PAIS', 'USU_VALIDADO_REGISTRADURIA', 'USU_BORRADO', 'USU_FOTO', 'MUN_ID', 'ROL_ID', 'DEP_ID', 'created_at', 'updated_at', 'email_verified_at', 'USU_CODIGOCONFIRMACION', 'COM_ID'],

			'required'		=> ['USU_TIPO_DOCUMENTO', 'USU_DOCUMENTO', 'USU_NOMBRES', 'USU_APELLIDOS'],

			'uniques'		=> [],

			'rules' 		=> [
				'USU_ID' => ['type' => 'int'],
				'USU_TIPO_DOCUMENTO' => ['type' => 'str', 'max' => 4, 'required' => true],
				'USU_DOCUMENTO' => ['type' => 'str', 'max' => 15, 'required' => true],
				'USU_CONTRASENA' => ['type' => 'str', 'max' => 500],
				'USU_MUNICIPIO_EXPEDICION_DOCUMENTO' => ['type' => 'int'],
				'USU_NOMBRES' => ['type' => 'str', 'max' => 100, 'required' => true],
				'USU_APELLIDOS' => ['type' => 'str', 'max' => 100, 'required' => true],
				'USU_DIRECCION' => ['type' => 'str', 'max' => 100],
				'USU_LUGAR_RESIDENCIA' => ['type' => 'str', 'max' => 50],
				'USU_CELULAR' => ['type' => 'str', 'max' => 50],
				'USU_TELEFONO' => ['type' => 'str', 'max' => 50],
				'USU_EMAIL' => ['type' => 'str', 'max' => 250],
				'USU_GENERO' => ['type' => 'str', 'max' => 10],
				'ASO_ID' => ['type' => 'int'],
				'USU_PARTICIPO_PL' => ['type' => 'bool'],
				'USU_PARTICIPO_PP' => ['type' => 'bool'],
				'USU_FECHA_NACIMIENTO' => ['type' => 'date'],
				'USU_FECHA_EXPEDICION' => ['type' => 'date'],
				'USU_FECHA_ACEPTACION_TERMINOS' => ['type' => 'timestamp'],
				'USU_FECHA_REGISTRO' => ['type' => 'timestamp'],
				'BAR_ID' => ['type' => 'int'],
				'USU_ESTADO' => ['type' => 'str', 'max' => 10],
				'USU_PAIS' => ['type' => 'str', 'max' => 100],
				'USU_VALIDADO_REGISTRADURIA' => ['type' => 'bool'],
				'USU_BORRADO' => ['type' => 'bool'],
				'USU_FOTO' => ['type' => 'str'],
				'MUN_ID' => ['type' => 'int', 'min' => 0],
				'ROL_ID' => ['type' => 'int'],
				'DEP_ID' => ['type' => 'int', 'min' => 0],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp'],
				'email_verified_at' => ['type' => 'bool'],
				'USU_CODIGOCONFIRMACION' => ['type' => 'str', 'max' => 45],
				'COM_ID' => ['type' => 'int']
			],

			'fks' 			=> ['ASO_ID', 'BAR_ID', 'COM_ID', 'DEP_ID', 'MUN_ID', 'ROL_ID'],

			'relationships' => [
				'TBL_ASOCIACIONES' => [
					['TBL_ASOCIACIONES.ASO_ID','TBL_USUARIOS.ASO_ID']
				],
				'TBL_BARRIOS' => [
					['TBL_BARRIOS.BAR_ID','TBL_USUARIOS.BAR_ID']
				],
				'TBL_COMUNAS' => [
					['TBL_COMUNAS.COM_ID','TBL_USUARIOS.COM_ID']
				],
				'TBL_DEPARTAMENTOS' => [
					['TBL_DEPARTAMENTOS.DEP_ID','TBL_USUARIOS.DEP_ID']
				],
				'TBL_MUNICIPIOS' => [
					['TBL_MUNICIPIOS.MUN_ID','TBL_USUARIOS.MUN_ID']
				],
				'TBL_ROLES' => [
					['TBL_ROLES.ROL_ID','TBL_USUARIOS.ROL_ID']
				],
				'TBL_TEMATICAS' => [
					['TBL_TEMATICAS.USU_ID','TBL_USUARIOS.USU_ID']
				],
				'TBL_ELECCIONES' => [
					['TBL_ELECCIONES.USU_ID','TBL_USUARIOS.USU_ID']
				],
				'TBL_PARTICIPACION_SONDEO' => [
					['TBL_PARTICIPACION_SONDEO.USU_ID','TBL_USUARIOS.USU_ID']
				],
				'TBL_USERS_TOKEN' => [
					['TBL_USERS_TOKEN.USU_ID','TBL_USUARIOS.USU_ID']
				],
				'TBL_PARTICIPACION_DEBATE' => [
					['TBL_PARTICIPACION_DEBATE.USU_ID','TBL_USUARIOS.USU_ID']
				],
				'TBL_PARTICIPACION_PROPUESTA' => [
					['TBL_PARTICIPACION_PROPUESTA.USU_ID','TBL_USUARIOS.USU_ID']
				],
				'TBL_PROCESOS' => [
					['TBL_PROCESOS.USU_ID','TBL_USUARIOS.USU_ID']
				],
				'TBL_DEBATES' => [
					['TBL_DEBATES.USU_ID','TBL_USUARIOS.USU_ID']
				],
				'TBL_LOGS' => [
					['TBL_LOGS.USU_ID','TBL_USUARIOS.USU_ID']
				],
				'TBL_PARTICIPACION_ELECCION' => [
					['TBL_PARTICIPACION_ELECCION.USU_ID','TBL_USUARIOS.USU_ID']
				],
				'TBL_SONDEOS' => [
					['TBL_SONDEOS.USU_ID','TBL_USUARIOS.USU_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_ASOCIACIONES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ASOCIACIONES',
        1 => 'ASO_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'ASO_ID',
      ),
    ),
  ),
  'TBL_BARRIOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_BARRIOS',
        1 => 'BAR_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'BAR_ID',
      ),
    ),
  ),
  'TBL_COMUNAS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_COMUNAS',
        1 => 'COM_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'COM_ID',
      ),
    ),
  ),
  'TBL_DEPARTAMENTOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_DEPARTAMENTOS',
        1 => 'DEP_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'DEP_ID',
      ),
    ),
  ),
  'TBL_MUNICIPIOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_MUNICIPIOS',
        1 => 'MUN_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'MUN_ID',
      ),
    ),
  ),
  'TBL_ROLES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ROLES',
        1 => 'ROL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'ROL_ID',
      ),
    ),
  ),
  'TBL_TEMATICAS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_TEMATICAS',
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'USU_ID',
      ),
    ),
  ),
  'TBL_ELECCIONES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ELECCIONES',
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'USU_ID',
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
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'USU_ID',
      ),
    ),
  ),
  'TBL_USERS_TOKEN' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_USERS_TOKEN',
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'USU_ID',
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
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'USU_ID',
      ),
    ),
  ),
  'TBL_PARTICIPACION_PROPUESTA' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PARTICIPACION_PROPUESTA',
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'USU_ID',
      ),
    ),
  ),
  'TBL_PROCESOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_PROCESOS',
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'USU_ID',
      ),
    ),
  ),
  'TBL_DEBATES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_DEBATES',
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'USU_ID',
      ),
    ),
  ),
  'TBL_LOGS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_LOGS',
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
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
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'USU_ID',
      ),
    ),
  ),
  'TBL_SONDEOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_SONDEOS',
        1 => 'USU_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'USU_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_ASOCIACIONES' => [
					['TBL_ASOCIACIONES.ASO_ID','TBL_USUARIOS.ASO_ID']
				],
				'TBL_BARRIOS' => [
					['TBL_BARRIOS.BAR_ID','TBL_USUARIOS.BAR_ID']
				],
				'TBL_COMUNAS' => [
					['TBL_COMUNAS.COM_ID','TBL_USUARIOS.COM_ID']
				],
				'TBL_DEPARTAMENTOS' => [
					['TBL_DEPARTAMENTOS.DEP_ID','TBL_USUARIOS.DEP_ID']
				],
				'TBL_MUNICIPIOS' => [
					['TBL_MUNICIPIOS.MUN_ID','TBL_USUARIOS.MUN_ID']
				],
				'TBL_ROLES' => [
					['TBL_ROLES.ROL_ID','TBL_USUARIOS.ROL_ID']
				]
			],

			'expanded_relationships_from' => array (
  'TBL_ASOCIACIONES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ASOCIACIONES',
        1 => 'ASO_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'ASO_ID',
      ),
    ),
  ),
  'TBL_BARRIOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_BARRIOS',
        1 => 'BAR_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'BAR_ID',
      ),
    ),
  ),
  'TBL_COMUNAS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_COMUNAS',
        1 => 'COM_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'COM_ID',
      ),
    ),
  ),
  'TBL_DEPARTAMENTOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_DEPARTAMENTOS',
        1 => 'DEP_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'DEP_ID',
      ),
    ),
  ),
  'TBL_MUNICIPIOS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_MUNICIPIOS',
        1 => 'MUN_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'MUN_ID',
      ),
    ),
  ),
  'TBL_ROLES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ROLES',
        1 => 'ROL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_USUARIOS',
        1 => 'ROL_ID',
      ),
    ),
  ),
)
		];
	}	
}

