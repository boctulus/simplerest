<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEmpleadoDatosGeneralesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_empleado_datos_generales',

			'id_name'		=> 'edg_intId',

			'attr_types'	=> [
				'edg_intId' => 'INT',
				'edg_datFechaExpCedula' => 'STR',
				'edg_varVivienda' => 'STR',
				'edg_varLugarResidencia' => 'STR',
				'edg_varDireccion' => 'STR',
				'edg_varTipoVia' => 'STR',
				'edg_varNumero' => 'STR',
				'edg_varLetra' => 'STR',
				'edg_varCuadrante' => 'STR',
				'edg_intTelefono' => 'INT',
				'edg_intMovil' => 'INT',
				'edg_varEmail' => 'STR',
				'edg_lonNota' => 'STR',
				'epg_dtimFechaCreacion' => 'STR',
				'epg_dtimFechaActualizacion' => 'STR',
				'pai_intIdPaisExpCedula' => 'INT',
				'per_intIdPersona' => 'INT',
				'ciu_intIdCiudadExpCedula' => 'INT',
				'dep_intIdDepartaExpCedula' => 'INT',
				'pai_intIdPais' => 'INT',
				'ciu_intIdCiudad' => 'INT',
				'dep_intIdDepartamento' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['edg_intId'],

			'autoincrement' => 'edg_intId',

			'nullable'		=> ['edg_intId', 'edg_datFechaExpCedula', 'edg_varVivienda', 'edg_varLugarResidencia', 'edg_varDireccion', 'edg_varTipoVia', 'edg_varNumero', 'edg_varLetra', 'edg_varCuadrante', 'edg_intTelefono', 'edg_intMovil', 'edg_varEmail', 'edg_lonNota', 'epg_dtimFechaCreacion', 'epg_dtimFechaActualizacion', 'pai_intIdPaisExpCedula', 'per_intIdPersona', 'ciu_intIdCiudadExpCedula', 'dep_intIdDepartaExpCedula', 'pai_intIdPais', 'ciu_intIdCiudad', 'dep_intIdDepartamento', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'edg_intId' => ['type' => 'int'],
				'edg_datFechaExpCedula' => ['type' => 'date'],
				'edg_varVivienda' => ['type' => 'str', 'max' => 250],
				'edg_varLugarResidencia' => ['type' => 'str', 'max' => 250],
				'edg_varDireccion' => ['type' => 'str', 'max' => 350],
				'edg_varTipoVia' => ['type' => 'str', 'max' => 200],
				'edg_varNumero' => ['type' => 'str', 'max' => 200],
				'edg_varLetra' => ['type' => 'str', 'max' => 200],
				'edg_varCuadrante' => ['type' => 'str', 'max' => 200],
				'edg_intTelefono' => ['type' => 'int'],
				'edg_intMovil' => ['type' => 'int'],
				'edg_varEmail' => ['type' => 'str', 'max' => 250],
				'edg_lonNota' => ['type' => 'str'],
				'epg_dtimFechaCreacion' => ['type' => 'date'],
				'epg_dtimFechaActualizacion' => ['type' => 'date'],
				'pai_intIdPaisExpCedula' => ['type' => 'int'],
				'per_intIdPersona' => ['type' => 'int'],
				'ciu_intIdCiudadExpCedula' => ['type' => 'int'],
				'dep_intIdDepartaExpCedula' => ['type' => 'int'],
				'pai_intIdPais' => ['type' => 'int'],
				'ciu_intIdCiudad' => ['type' => 'int'],
				'dep_intIdDepartamento' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['ciu_intIdCiudadExpCedula', 'ciu_intIdCiudad', 'dep_intIdDepartamento', 'dep_intIdDepartaExpCedula', 'est_intIdEstado', 'pai_intIdPais', 'pai_intIdPaisExpCedula', 'per_intIdPersona', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_ciudad' => [
					['tbl_ciudad|__ciu_intIdCiudadExpCedula.ciu_intId','tbl_empleado_datos_generales.ciu_intIdCiudadExpCedula'],
					['tbl_ciudad|__ciu_intIdCiudad.ciu_intId','tbl_empleado_datos_generales.ciu_intIdCiudad']
				],
				'tbl_departamento' => [
					['tbl_departamento|__dep_intIdDepartamento.dep_intId','tbl_empleado_datos_generales.dep_intIdDepartamento'],
					['tbl_departamento|__dep_intIdDepartaExpCedula.dep_intId','tbl_empleado_datos_generales.dep_intIdDepartaExpCedula']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_empleado_datos_generales.est_intIdEstado']
				],
				'tbl_pais' => [
					['tbl_pais|__pai_intIdPais.pai_intId','tbl_empleado_datos_generales.pai_intIdPais'],
					['tbl_pais|__pai_intIdPaisExpCedula.pai_intId','tbl_empleado_datos_generales.pai_intIdPaisExpCedula']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_empleado_datos_generales.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_empleado_datos_generales.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_empleado_datos_generales.usu_intIdCreador']
				]
			],

			'expanded_relationships' => array (
				  'tbl_ciudad' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'ciu_intId',
				        'alias' => '__ciu_intIdCiudadExpCedula',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'ciu_intIdCiudadExpCedula',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'ciu_intId',
				        'alias' => '__ciu_intIdCiudad',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'ciu_intIdCiudad',
				      ),
				    ),
				  ),
				  'tbl_departamento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_departamento',
				        1 => 'dep_intId',
				        'alias' => '__dep_intIdDepartamento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'dep_intIdDepartamento',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_departamento',
				        1 => 'dep_intId',
				        'alias' => '__dep_intIdDepartaExpCedula',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'dep_intIdDepartaExpCedula',
				      ),
				    ),
				  ),
				  'tbl_estado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_pais' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pais',
				        1 => 'pai_intId',
				        'alias' => '__pai_intIdPais',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'pai_intIdPais',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pais',
				        1 => 'pai_intId',
				        'alias' => '__pai_intIdPaisExpCedula',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'pai_intIdPaisExpCedula',
				      ),
				    ),
				  ),
				  'tbl_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'per_intIdPersona',
				      ),
				    ),
				  ),
				  'tbl_usuario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_ciudad' => [
					['tbl_ciudad|__ciu_intIdCiudadExpCedula.ciu_intId','tbl_empleado_datos_generales.ciu_intIdCiudadExpCedula'],
					['tbl_ciudad|__ciu_intIdCiudad.ciu_intId','tbl_empleado_datos_generales.ciu_intIdCiudad']
				],
				'tbl_departamento' => [
					['tbl_departamento|__dep_intIdDepartamento.dep_intId','tbl_empleado_datos_generales.dep_intIdDepartamento'],
					['tbl_departamento|__dep_intIdDepartaExpCedula.dep_intId','tbl_empleado_datos_generales.dep_intIdDepartaExpCedula']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_empleado_datos_generales.est_intIdEstado']
				],
				'tbl_pais' => [
					['tbl_pais|__pai_intIdPais.pai_intId','tbl_empleado_datos_generales.pai_intIdPais'],
					['tbl_pais|__pai_intIdPaisExpCedula.pai_intId','tbl_empleado_datos_generales.pai_intIdPaisExpCedula']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_empleado_datos_generales.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_empleado_datos_generales.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_empleado_datos_generales.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_ciudad' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'ciu_intId',
				        'alias' => '__ciu_intIdCiudadExpCedula',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'ciu_intIdCiudadExpCedula',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'ciu_intId',
				        'alias' => '__ciu_intIdCiudad',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'ciu_intIdCiudad',
				      ),
				    ),
				  ),
				  'tbl_departamento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_departamento',
				        1 => 'dep_intId',
				        'alias' => '__dep_intIdDepartamento',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'dep_intIdDepartamento',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_departamento',
				        1 => 'dep_intId',
				        'alias' => '__dep_intIdDepartaExpCedula',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'dep_intIdDepartaExpCedula',
				      ),
				    ),
				  ),
				  'tbl_estado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_pais' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pais',
				        1 => 'pai_intId',
				        'alias' => '__pai_intIdPais',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'pai_intIdPais',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pais',
				        1 => 'pai_intId',
				        'alias' => '__pai_intIdPaisExpCedula',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'pai_intIdPaisExpCedula',
				      ),
				    ),
				  ),
				  'tbl_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'per_intIdPersona',
				      ),
				    ),
				  ),
				  'tbl_usuario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_generales',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

