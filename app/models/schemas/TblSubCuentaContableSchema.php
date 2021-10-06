<?php

namespace simplerest\models\schemas;

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

			'nullable'		=> ['sub_intId', 'sub_intPorcentajeBase', 'sub_decMontobase', 'sub_tinCuentaBalance', 'sub_tinCuentaResultado', 'sub_dtimFechaCreacion', 'sub_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'sub_decMontobase' => ['type' => 'str'],
				'sub_intId' => ['type' => 'int'],
				'sub_varCodigoCuenta' => ['type' => 'str', 'max' => 20, 'required' => true],
				'sub_varNombreCuenta' => ['type' => 'str', 'max' => 50, 'required' => true],
				'sub_varConceptoMedioMagnetico' => ['type' => 'str', 'max' => 50, 'required' => true],
				'sub_varEquivalenciaFisica' => ['type' => 'str', 'max' => 50, 'required' => true],
				'sub_tinManejaTercero' => ['type' => 'bool', 'required' => true],
				'sub_tinManejaCentroCostos' => ['type' => 'bool', 'required' => true],
				'sub_tinManejaBase' => ['type' => 'bool', 'required' => true],
				'sub_intPorcentajeBase' => ['type' => 'int'],
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
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_sub_cuenta_contable.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_sub_cuenta_contable.usu_intIdCreador']
				],
				'tbl_proveedor_informacion_tributaria' => [
					['tbl_proveedor_informacion_tributaria.sub_intIdSubCuentaContable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_retencion_cuentacontable' => [
					['tbl_retencion_cuentacontable.rec_intIdCuentaContable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_iva_cuentacontable' => [
					['tbl_iva_cuentacontable.ivc_intIdCuentaContable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_producto' => [
					['tbl_producto.sub_intIdCuentaContableCompra','tbl_sub_cuenta_contable.sub_intId'],
					['tbl_producto.sub_intIdCuentaContableVenta','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_banco' => [
					['tbl_banco.sub_intIdCuentaCxC','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_iva' => [
					['tbl_iva.sub_intIdCuentaContable','tbl_sub_cuenta_contable.sub_intId']
				],
				'tbl_cliente_informacion_tributaria' => [
					['tbl_cliente_informacion_tributaria.sub_intIdSubcuentacontable','tbl_sub_cuenta_contable.sub_intId']
				]
			]
		];
	}	
}

