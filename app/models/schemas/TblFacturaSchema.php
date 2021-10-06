<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblFacturaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_factura',

			'id_name'		=> 'fac_intId',

			'attr_types'	=> [
				'fac_intId' => 'INT',
				'fac_varNroDocumento' => 'STR',
				'fac_decCantidadTotal' => 'STR',
				'fac_decBruto' => 'STR',
				'fac_decDescuento' => 'STR',
				'fac_decIva' => 'STR',
				'fac_decIca' => 'STR',
				'fac_decRetencion' => 'STR',
				'fac_decReteIva' => 'STR',
				'fac_dateFecha' => 'STR',
				'fac_decNeto' => 'STR',
				'fac_bolEstado' => 'INT',
				'fac_dateFechaVencimiento' => 'STR',
				'fac_decPorceRetefuente' => 'STR',
				'fac_intTopeRetefuente' => 'INT',
				'fac_decPorceReteiva' => 'STR',
				'fac_intTopeReteiva' => 'INT',
				'fac_decPorceIca' => 'STR',
				'fac_intTopeReteIca' => 'INT',
				'fac_dtimFechaCreacion' => 'STR',
				'fac_dtimFechaActualizacion' => 'STR',
				'fac_varNota' => 'STR',
				'fac_bolPagado' => 'INT',
				'cen_intIdCentrocostos' => 'INT',
				'doc_intDocumento' => 'INT',
				'cse_intIdConsecutivo' => 'INT',
				'per_intIdPersona' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['fac_intId', 'fac_bolEstado', 'fac_dtimFechaCreacion', 'fac_dtimFechaActualizacion', 'fac_bolPagado'],

			'rules' 		=> [
				'fac_decCantidadTotal' => ['type' => 'str', 'required' => true],
				'fac_decBruto' => ['type' => 'str', 'required' => true],
				'fac_decDescuento' => ['type' => 'str', 'required' => true],
				'fac_decIva' => ['type' => 'str', 'required' => true],
				'fac_decIca' => ['type' => 'str', 'required' => true],
				'fac_decRetencion' => ['type' => 'str', 'required' => true],
				'fac_decReteIva' => ['type' => 'str', 'required' => true],
				'fac_decNeto' => ['type' => 'str', 'required' => true],
				'fac_decPorceRetefuente' => ['type' => 'str', 'required' => true],
				'fac_decPorceReteiva' => ['type' => 'str', 'required' => true],
				'fac_decPorceIca' => ['type' => 'str', 'required' => true],
				'fac_intId' => ['type' => 'int'],
				'fac_varNroDocumento' => ['type' => 'str', 'max' => 20, 'required' => true],
				'fac_dateFecha' => ['type' => 'date', 'required' => true],
				'fac_bolEstado' => ['type' => 'bool'],
				'fac_dateFechaVencimiento' => ['type' => 'date', 'required' => true],
				'fac_intTopeRetefuente' => ['type' => 'int', 'required' => true],
				'fac_intTopeReteiva' => ['type' => 'int', 'required' => true],
				'fac_intTopeReteIca' => ['type' => 'int', 'required' => true],
				'fac_dtimFechaCreacion' => ['type' => 'datetime'],
				'fac_dtimFechaActualizacion' => ['type' => 'datetime'],
				'fac_varNota' => ['type' => 'str', 'required' => true],
				'fac_bolPagado' => ['type' => 'bool'],
				'cen_intIdCentrocostos' => ['type' => 'int', 'required' => true],
				'doc_intDocumento' => ['type' => 'int', 'required' => true],
				'cse_intIdConsecutivo' => ['type' => 'int', 'required' => true],
				'per_intIdPersona' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_centro_costos' => [
					['tbl_centro_costos.cco_intId','tbl_factura.cen_intIdCentrocostos']
				],
				'tbl_consecutivo' => [
					['tbl_consecutivo.cse_intId','tbl_factura.cse_intIdConsecutivo']
				],
				'tbl_documento' => [
					['tbl_documento.doc_intId','tbl_factura.doc_intDocumento']
				],
				'tbl_persona' => [
					['tbl_persona.per_intId','tbl_factura.per_intIdPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_factura.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_factura.usu_intIdActualizador']
				],
				'tbl_factura_detalle' => [
					['tbl_factura_detalle.fac_intIdFactura','tbl_factura.fac_intId']
				]
			]
		];
	}	
}

