<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblInformacionTributariaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_informacion_tributaria',

			'id_name'		=> 'tft_intId',

			'attr_types'	=> [
				'tft_intId' => 'INT',
				'tft_bolGrancontribuyente' => 'INT',
				'tft_bolLLevarContabilidad' => 'INT',
				'tft_bolCalculaIca' => 'INT',
				'tft_dtimFechaCreacion' => 'STR',
				'tft_dtimFechaActualizacion' => 'STR',
				'per_intIdpersona' => 'INT',
				'sub_intIdcxp_subcuentacontable' => 'INT',
				'sub_intIdcxc_subcuentacontable' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['tft_intId'],

			'autoincrement' => 'tft_intId',

			'nullable'		=> ['tft_intId', 'tft_bolGrancontribuyente', 'tft_bolLLevarContabilidad', 'tft_bolCalculaIca', 'tft_dtimFechaCreacion', 'tft_dtimFechaActualizacion', 'per_intIdpersona', 'sub_intIdcxp_subcuentacontable', 'sub_intIdcxc_subcuentacontable', 'est_intIdEstado'],

			'uniques'		=> ['per_intIdpersona'],

			'rules' 		=> [
				'tft_intId' => ['type' => 'int'],
				'tft_bolGrancontribuyente' => ['type' => 'bool'],
				'tft_bolLLevarContabilidad' => ['type' => 'bool'],
				'tft_bolCalculaIca' => ['type' => 'bool'],
				'tft_dtimFechaCreacion' => ['type' => 'datetime'],
				'tft_dtimFechaActualizacion' => ['type' => 'datetime'],
				'per_intIdpersona' => ['type' => 'int'],
				'sub_intIdcxp_subcuentacontable' => ['type' => 'int'],
				'sub_intIdcxc_subcuentacontable' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'per_intIdpersona', 'sub_intIdcxc_subcuentacontable', 'sub_intIdcxp_subcuentacontable', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_informacion_tributaria.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_informacion_tributaria.per_intIdpersona']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable|__sub_intIdcxc_subcuentacontable.sub_intId','tbl_informacion_tributaria.sub_intIdcxc_subcuentacontable'],
					['tbl_sub_cuenta_contable|__sub_intIdcxp_subcuentacontable.sub_intId','tbl_informacion_tributaria.sub_intIdcxp_subcuentacontable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_informacion_tributaria.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_informacion_tributaria.usu_intIdCreador']
				]
			],

			'expanded_relationships' => array (
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
				        0 => 'tbl_informacion_tributaria',
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
				        0 => 'tbl_informacion_tributaria',
				        1 => 'per_intIdpersona',
				      ),
				    ),
				  ),
				  'tbl_sub_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				        'alias' => '__sub_intIdcxc_subcuentacontable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_informacion_tributaria',
				        1 => 'sub_intIdcxc_subcuentacontable',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				        'alias' => '__sub_intIdcxp_subcuentacontable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_informacion_tributaria',
				        1 => 'sub_intIdcxp_subcuentacontable',
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
				        0 => 'tbl_informacion_tributaria',
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
				        0 => 'tbl_informacion_tributaria',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_informacion_tributaria.est_intIdEstado']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_informacion_tributaria.per_intIdpersona']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable|__sub_intIdcxc_subcuentacontable.sub_intId','tbl_informacion_tributaria.sub_intIdcxc_subcuentacontable'],
					['tbl_sub_cuenta_contable|__sub_intIdcxp_subcuentacontable.sub_intId','tbl_informacion_tributaria.sub_intIdcxp_subcuentacontable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_informacion_tributaria.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_informacion_tributaria.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
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
				        0 => 'tbl_informacion_tributaria',
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
				        0 => 'tbl_informacion_tributaria',
				        1 => 'per_intIdpersona',
				      ),
				    ),
				  ),
				  'tbl_sub_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				        'alias' => '__sub_intIdcxc_subcuentacontable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_informacion_tributaria',
				        1 => 'sub_intIdcxc_subcuentacontable',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				        'alias' => '__sub_intIdcxp_subcuentacontable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_informacion_tributaria',
				        1 => 'sub_intIdcxp_subcuentacontable',
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
				        0 => 'tbl_informacion_tributaria',
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
				        0 => 'tbl_informacion_tributaria',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

