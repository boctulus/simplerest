<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEmpleadoDatosPersonalesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_empleado_datos_personales',

			'id_name'		=> 'edp_intId',

			'attr_types'	=> [
				'edp_intId' => 'INT',
				'epd_decEstatura' => 'STR',
				'epd_decPeso' => 'STR',
				'epd_intNumeroLibretaMilitar' => 'INT',
				'epd_varMotivoNoPrestoServicio' => 'STR',
				'epd_intNumeroLicencia' => 'INT',
				'epd_dtimFechaCreacion' => 'STR',
				'epd_dtimFechaActualizacion' => 'STR',
				'esc_intIdEstadoCivil' => 'INT',
				'per_intIdPersona' => 'INT',
				'esd_intIdEstudios' => 'INT',
				'trh_intIdRH' => 'INT',
				'clm_intIdClaseLibretaMilitar' => 'INT',
				'clc_intIdCategoriaLicencia' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['edp_intId'],

			'autoincrement' => 'edp_intId',

			'nullable'		=> ['edp_intId', 'epd_decEstatura', 'epd_decPeso', 'epd_intNumeroLibretaMilitar', 'epd_varMotivoNoPrestoServicio', 'epd_intNumeroLicencia', 'epd_dtimFechaCreacion', 'epd_dtimFechaActualizacion', 'esc_intIdEstadoCivil', 'per_intIdPersona', 'esd_intIdEstudios', 'trh_intIdRH', 'clm_intIdClaseLibretaMilitar', 'clc_intIdCategoriaLicencia', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'edp_intId' => ['type' => 'int'],
				'epd_decEstatura' => ['type' => 'decimal(18,2)'],
				'epd_decPeso' => ['type' => 'decimal(18,2)'],
				'epd_intNumeroLibretaMilitar' => ['type' => 'int'],
				'epd_varMotivoNoPrestoServicio' => ['type' => 'str', 'max' => 350],
				'epd_intNumeroLicencia' => ['type' => 'int'],
				'epd_dtimFechaCreacion' => ['type' => 'date'],
				'epd_dtimFechaActualizacion' => ['type' => 'date'],
				'esc_intIdEstadoCivil' => ['type' => 'int'],
				'per_intIdPersona' => ['type' => 'int'],
				'esd_intIdEstudios' => ['type' => 'int'],
				'trh_intIdRH' => ['type' => 'int'],
				'clm_intIdClaseLibretaMilitar' => ['type' => 'int'],
				'clc_intIdCategoriaLicencia' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int'],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['clc_intIdCategoriaLicencia', 'clm_intIdClaseLibretaMilitar', 'esc_intIdEstadoCivil', 'esd_intIdEstudios', 'est_intIdEstado', 'per_intIdPersona', 'trh_intIdRH', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_categoria_licencia_conduccion' => [
					['tbl_categoria_licencia_conduccion.clc_intId','tbl_empleado_datos_personales.clc_intIdCategoriaLicencia']
				],
				'tbl_clase_libreta_militar' => [
					['tbl_clase_libreta_militar.clm_intId','tbl_empleado_datos_personales.clm_intIdClaseLibretaMilitar']
				],
				'tbl_estado_civil' => [
					['tbl_estado_civil.esc_intId','tbl_empleado_datos_personales.esc_intIdEstadoCivil']
				],
				'tbl_estudios' => [
					['tbl_estudios.esd_intId','tbl_empleado_datos_personales.esd_intIdEstudios']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_empleado_datos_personales.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_empleado_datos_personales.per_intIdPersona']
				],
				'tbl_rh' => [
					['tbl_rh.trh_intId','tbl_empleado_datos_personales.trh_intIdRH']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_empleado_datos_personales.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_empleado_datos_personales.usu_intIdCreador']
				]
			],

			'expanded_relationships' => array (
				  'tbl_categoria_licencia_conduccion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_licencia_conduccion',
				        1 => 'clc_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'clc_intIdCategoriaLicencia',
				      ),
				    ),
				  ),
				  'tbl_clase_libreta_militar' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_clase_libreta_militar',
				        1 => 'clm_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'clm_intIdClaseLibretaMilitar',
				      ),
				    ),
				  ),
				  'tbl_estado_civil' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado_civil',
				        1 => 'esc_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'esc_intIdEstadoCivil',
				      ),
				    ),
				  ),
				  'tbl_estudios' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estudios',
				        1 => 'esd_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'esd_intIdEstudios',
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
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'per_intIdPersona',
				      ),
				    ),
				  ),
				  'tbl_rh' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_rh',
				        1 => 'trh_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'trh_intIdRH',
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
				        0 => 'tbl_empleado_datos_personales',
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
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_categoria_licencia_conduccion' => [
					['tbl_categoria_licencia_conduccion.clc_intId','tbl_empleado_datos_personales.clc_intIdCategoriaLicencia']
				],
				'tbl_clase_libreta_militar' => [
					['tbl_clase_libreta_militar.clm_intId','tbl_empleado_datos_personales.clm_intIdClaseLibretaMilitar']
				],
				'tbl_estado_civil' => [
					['tbl_estado_civil.esc_intId','tbl_empleado_datos_personales.esc_intIdEstadoCivil']
				],
				'tbl_estudios' => [
					['tbl_estudios.esd_intId','tbl_empleado_datos_personales.esd_intIdEstudios']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_empleado_datos_personales.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_empleado_datos_personales.per_intIdPersona']
				],
				'tbl_rh' => [
					['tbl_rh.trh_intId','tbl_empleado_datos_personales.trh_intIdRH']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_empleado_datos_personales.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_empleado_datos_personales.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_categoria_licencia_conduccion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_licencia_conduccion',
				        1 => 'clc_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'clc_intIdCategoriaLicencia',
				      ),
				    ),
				  ),
				  'tbl_clase_libreta_militar' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_clase_libreta_militar',
				        1 => 'clm_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'clm_intIdClaseLibretaMilitar',
				      ),
				    ),
				  ),
				  'tbl_estado_civil' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado_civil',
				        1 => 'esc_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'esc_intIdEstadoCivil',
				      ),
				    ),
				  ),
				  'tbl_estudios' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estudios',
				        1 => 'esd_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'esd_intIdEstudios',
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
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'est_intIdEstado',
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
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'per_intIdPersona',
				      ),
				    ),
				  ),
				  'tbl_rh' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_rh',
				        1 => 'trh_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'trh_intIdRH',
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
				        0 => 'tbl_empleado_datos_personales',
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
				        0 => 'tbl_empleado_datos_personales',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

