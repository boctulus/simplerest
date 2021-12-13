<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblSubCuentaContableSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_sub_cuenta_contable',

			'id_name'		=> 'sub_intId',

			'attr_types'	=> [
				'sub_intId' => 'INT',
				'sub_varCodigoCuenta' => 'STR',
				'sub_varNombreCuenta' => 'STR',
				'sub_varConceptoMedioMagnetico' => 'STR',
				'sub_varEquivalenciaFisica' => 'STR',
				'sub_tinManejaTercero' => 'INT',
				'sub_tinManejaCentroCostos' => 'INT',
				'sub_tinManejaBase' => 'INT',
				'sub_intPorcentajeBase' => 'INT',
				'sub_decMontobase' => 'STR',
				'sub_tinCuentaBalance' => 'INT',
				'sub_tinCuentaResultado' => 'INT',
				'sub_dtimFechaCreacion' => 'STR',
				'sub_dtimFechaActualizacion' => 'STR',
				'mon_intIdMoneda' => 'INT',
				'ccc_intIdCategoriaCuentaContable' => 'INT',
				'cue_intIdCuentaContable' => 'INT',
				'nat_intIdNaturalezaCuentaContable' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['sub_intId'],

			'autoincrement' => 'sub_intId',

			'nullable'		=> ['sub_intId', 'sub_intPorcentajeBase', 'sub_decMontobase', 'sub_tinCuentaBalance', 'sub_tinCuentaResultado', 'sub_dtimFechaCreacion', 'sub_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> ['sub_varCodigoCuenta'],

			'rules' 		=> [
				'sub_intId' => ['type' => 'int'],
				'sub_varCodigoCuenta' => ['type' => 'str', 'max' => 20, 'required' => true],
				'sub_varNombreCuenta' => ['type' => 'str', 'max' => 50, 'required' => true],
				'sub_varConceptoMedioMagnetico' => ['type' => 'str', 'max' => 50, 'required' => true],
				'sub_varEquivalenciaFisica' => ['type' => 'str', 'max' => 50, 'required' => true],
				'sub_tinManejaTercero' => ['type' => 'bool', 'required' => true],
				'sub_tinManejaCentroCostos' => ['type' => 'bool', 'required' => true],
				'sub_tinManejaBase' => ['type' => 'bool', 'required' => true],
				'sub_intPorcentajeBase' => ['type' => 'int'],
				'sub_decMontobase' => ['type' => 'decimal(10,2)'],
				'sub_tinCuentaBalance' => ['type' => 'bool'],
				'sub_tinCuentaResultado' => ['type' => 'bool'],
				'sub_dtimFechaCreacion' => ['type' => 'datetime'],
				'sub_dtimFechaActualizacion' => ['type' => 'datetime'],
				'mon_intIdMoneda' => ['type' => 'int', 'required' => true],
				'ccc_intIdCategoriaCuentaContable' => ['type' => 'int', 'required' => true],
				'cue_intIdCuentaContable' => ['type' => 'int', 'required' => true],
				'nat_intIdNaturalezaCuentaContable' => ['type' => 'int', 'required' => true],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['cue_intIdCuentaContable', 'est_intIdEstado', 'mon_intIdMoneda', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_cuenta_contable' => [
					['tbl_cuenta_contable.cue_intId','tbl_sub_cuenta_contable.cue_intIdCuentaContable']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_sub_cuenta_contable.est_intIdEstado']
				],
				'tbl_moneda' => [
					['tbl_moneda.mon_intId','tbl_sub_cuenta_contable.mon_intIdMoneda']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_sub_cuenta_contable.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_sub_cuenta_contable.usu_intIdActualizador']
				],
				'tbl_informacion_tributaria' => [
					['tbl_informacion_tributaria.sub_intIdcxp_subcuentacontable','tbl_sub_cuenta_contable.sub_intId'],
					['tbl_informacion_tributaria.sub_intIdcxc_subcuentacontable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_banco' => [
					['tbl_banco.sub_intIdCuentaCxC','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_producto' => [
					['tbl_producto.sub_intIdCuentaContableCompra','tbl_sub_cuenta_contable.sub_intId'],
					['tbl_producto.sub_intIdCuentaContableVenta','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_cliente_retencion_cuentacontable' => [
					['tbl_cliente_retencion_cuentacontable.rcl_intIdCuentaContable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_rete_ica' => [
					['tbl_rete_ica.sub_intIdSubCuentaContable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_reteiva' => [
					['tbl_reteiva.sub_intIdsubcuentacontable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_cliente_reteiva_cuentacontable' => [
					['tbl_cliente_reteiva_cuentacontable.ric_intIdCuentacontable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_retencion' => [
					['tbl_retencion.sub_intIdsubcuentacontable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_iva' => [
					['tbl_iva.sub_intIdCuentaContable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_comprobante_contable_detalle' => [
					['tbl_comprobante_contable_detalle.sub_intIdCuentaContable','tbl_sub_cuenta_contable.sub_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cuenta_contable',
				        1 => 'cue_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'cue_intIdCuentaContable',
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
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_moneda' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_moneda',
				        1 => 'mon_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'mon_intIdMoneda',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				  'tbl_informacion_tributaria' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_informacion_tributaria',
				        1 => 'sub_intIdcxp_subcuentacontable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_informacion_tributaria',
				        1 => 'sub_intIdcxc_subcuentacontable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				    ),
				  ),
				  'tbl_banco' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_banco',
				        1 => 'sub_intIdCuentaCxC',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				    ),
				  ),
				  'tbl_producto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_producto',
				        1 => 'sub_intIdCuentaContableCompra',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_producto',
				        1 => 'sub_intIdCuentaContableVenta',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
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
				        1 => 'rcl_intIdCuentaContable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				    ),
				  ),
				  'tbl_rete_ica' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_rete_ica',
				        1 => 'sub_intIdSubCuentaContable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				    ),
				  ),
				  'tbl_reteiva' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_reteiva',
				        1 => 'sub_intIdsubcuentacontable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
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
				        1 => 'ric_intIdCuentacontable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				    ),
				  ),
				  'tbl_retencion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_retencion',
				        1 => 'sub_intIdsubcuentacontable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				    ),
				  ),
				  'tbl_iva' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_iva',
				        1 => 'sub_intIdCuentaContable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				    ),
				  ),
				  'tbl_comprobante_contable_detalle' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_comprobante_contable_detalle',
				        1 => 'sub_intIdCuentaContable',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_cuenta_contable' => [
					['tbl_cuenta_contable.cue_intId','tbl_sub_cuenta_contable.cue_intIdCuentaContable']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_sub_cuenta_contable.est_intIdEstado']
				],
				'tbl_moneda' => [
					['tbl_moneda.mon_intId','tbl_sub_cuenta_contable.mon_intIdMoneda']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_sub_cuenta_contable.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_sub_cuenta_contable.usu_intIdActualizador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cuenta_contable',
				        1 => 'cue_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'cue_intIdCuentaContable',
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
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_moneda' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_moneda',
				        1 => 'mon_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'mon_intIdMoneda',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

