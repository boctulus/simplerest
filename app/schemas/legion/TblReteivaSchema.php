<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblReteivaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_reteiva',

			'id_name'		=> 'riv_intId',

			'attr_types'	=> [
				'riv_intId' => 'INT',
				'riv_varReteIva' => 'STR',
				'riv_intTope' => 'INT',
				'riv_decPorcentaje' => 'STR',
				'riv_dtimFechaCreacion' => 'STR',
				'riv_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'sub_intIdsubcuentacontable' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['riv_intId'],

			'autoincrement' => 'riv_intId',

			'nullable'		=> ['riv_intId', 'riv_dtimFechaCreacion', 'riv_dtimFechaActualizacion', 'est_intIdEstado', 'sub_intIdsubcuentacontable'],

			'uniques'		=> ['riv_varReteIva'],

			'rules' 		=> [
				'riv_intId' => ['type' => 'int'],
				'riv_varReteIva' => ['type' => 'str', 'max' => 50, 'required' => true],
				'riv_intTope' => ['type' => 'int', 'required' => true],
				'riv_decPorcentaje' => ['type' => 'decimal(10,2)', 'required' => true],
				'riv_dtimFechaCreacion' => ['type' => 'datetime'],
				'riv_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'sub_intIdsubcuentacontable' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'sub_intIdsubcuentacontable', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_reteiva.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_reteiva.sub_intIdsubcuentacontable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_reteiva.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_reteiva.usu_intIdCreador']
				],
				'tbl_cliente_reteiva_cuentacontable' => [
					['tbl_cliente_reteiva_cuentacontable.ric_intIdReteiva','tbl_reteiva.riv_intId']
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
				        0 => 'tbl_reteiva',
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
				        0 => 'tbl_reteiva',
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
				        0 => 'tbl_reteiva',
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
				        0 => 'tbl_reteiva',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_cliente_reteiva_cuentacontable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente_reteiva_cuentacontable',
				        1 => 'ric_intIdReteiva',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_reteiva',
				        1 => 'riv_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_reteiva.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_reteiva.sub_intIdsubcuentacontable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_reteiva.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_reteiva.usu_intIdCreador']
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
				        0 => 'tbl_reteiva',
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
				        0 => 'tbl_reteiva',
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
				        0 => 'tbl_reteiva',
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
				        0 => 'tbl_reteiva',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

