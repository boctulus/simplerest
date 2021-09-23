<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblPersonaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_persona',

			'id_name'		=> 'per_intId',

			'attr_types'	=> [
				'per_intId' => 'INT',
				'per_varIdentificacion' => 'STR',
				'per_varDV' => 'STR',
				'per_varRazonSocial' => 'STR',
				'per_varNombre' => 'STR',
				'per_varNombre2' => 'STR',
				'per_varApellido' => 'STR',
				'per_varApellido2' => 'STR',
				'per_varNombreCompleto' => 'STR',
				'per_varDireccion' => 'STR',
				'per_varCelular' => 'STR',
				'per_varTelefono' => 'STR',
				'per_varEmail' => 'STR',
				'per_datFechaNacimiento' => 'STR',
				'per_dtimFechaCreacion' => 'STR',
				'per_dtimFechaActualizacion' => 'STR',
				'tpr_intIdTipoPersona' => 'INT',
				'pai_intIdPais' => 'INT',
				'ciu_intIdCiudad' => 'INT',
				'gen_intIdGenero' => 'INT',
				'cid_intIdCategoriIdentificacion' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['per_intId', 'per_varRazonSocial', 'per_varNombre', 'per_varNombre2', 'per_varApellido', 'per_varApellido2', 'per_varTelefono'],

			'rules' 		=> [
				'per_varIdentificacion' => ['max' => 20],
				'per_varDV' => ['max' => 1],
				'per_varRazonSocial' => ['max' => 200],
				'per_varNombre' => ['max' => 100],
				'per_varNombre2' => ['max' => 100],
				'per_varApellido' => ['max' => 100],
				'per_varApellido2' => ['max' => 100],
				'per_varDireccion' => ['max' => 255],
				'per_varCelular' => ['max' => 15],
				'per_varTelefono' => ['max' => 15],
				'per_varEmail' => ['max' => 100]
			],

			'relationships' => [
				'tbl_categoria_identificacion' => [
					['tbl_categoria_identificacion.cid_intId','tbl_persona.cid_intIdCategoriIdentificacion']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.ciu_intId','tbl_persona.ciu_intIdCiudad']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_persona.est_intIdEstado']
				],
				'tbl_genero' => [
					['tbl_genero.gen_intId','tbl_persona.gen_intIdGenero']
				],
				'tbl_pais' => [
					['tbl_pais.pai_intId','tbl_persona.pai_intIdPais']
				],
				'tbl_tipo_persona' => [
					['tbl_tipo_persona.tpr_intId','tbl_persona.tpr_intIdTipoPersona']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_persona.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_persona.usu_intIdCreador']
				],
				'tbl_cliente' => [
					['tbl_cliente.ali_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_factura' => [
					['tbl_factura.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.per_intIdPersona','tbl_persona.per_intId']
				]
			]
		];
	}	
}

