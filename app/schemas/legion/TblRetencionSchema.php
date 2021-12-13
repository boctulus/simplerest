<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblRetencionSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_retencion',

			'id_name'		=> 'ret_intId',

			'attr_types'	=> [
				'ret_intId' => 'INT',
				'ret_varRetencion' => 'STR',
				'ret_intTope' => 'INT',
				'ret_decPorcentaje' => 'STR',
				'ret_dtimFechaCreacion' => 'STR',
				'ret_dtimFechaActualizacion' => 'STR',
				'sub_intIdsubcuentacontable' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['ret_intId'],

			'autoincrement' => 'ret_intId',

			'nullable'		=> ['ret_intId', 'ret_dtimFechaCreacion', 'ret_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> ['ret_varRetencion'],

			'rules' 		=> [
				'ret_intId' => ['type' => 'int'],
				'ret_varRetencion' => ['type' => 'str', 'max' => 50, 'required' => true],
				'ret_intTope' => ['type' => 'int', 'required' => true],
				'ret_decPorcentaje' => ['type' => 'decimal(10,2)', 'required' => true],
				'ret_dtimFechaCreacion' => ['type' => 'datetime'],
				'ret_dtimFechaActualizacion' => ['type' => 'datetime'],
				'sub_intIdsubcuentacontable' => ['type' => 'int', 'required' => true],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'sub_intIdsubcuentacontable', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_retencion.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_retencion.sub_intIdsubcuentacontable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_retencion.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_retencion.usu_intIdCreador']
				],
				'tbl_cliente_retencion_cuentacontable' => [
					['tbl_cliente_retencion_cuentacontable.rcl_intIdRetencion','tbl_retencion.ret_intId']
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
				        0 => 'tbl_retencion',
				        1 => 'est_intIdEstado',
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
				      ),
				      1 => 
				      array (
				        0 => 'tbl_retencion',
				        1 => 'sub_intIdsubcuentacontable',
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
				        0 => 'tbl_retencion',
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
				        0 => 'tbl_retencion',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_cliente_retencion_cuentacontable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente_retencion_cuentacontable',
				        1 => 'rcl_intIdRetencion',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_retencion',
				        1 => 'ret_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_retencion.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_retencion.sub_intIdsubcuentacontable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_retencion.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_retencion.usu_intIdCreador']
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
				        0 => 'tbl_retencion',
				        1 => 'est_intIdEstado',
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
				      ),
				      1 => 
				      array (
				        0 => 'tbl_retencion',
				        1 => 'sub_intIdsubcuentacontable',
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
				        0 => 'tbl_retencion',
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
				        0 => 'tbl_retencion',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

