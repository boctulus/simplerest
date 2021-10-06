<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblUsuarioSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_usuario',

			'id_name'		=> 'usu_intId',

			'attr_types'	=> [
				'usu_intId' => 'INT',
				'usu_varNroIdentificacion' => 'STR',
				'usu_varNombre' => 'STR',
				'usu_varNombre2' => 'STR',
				'usu_varApellido' => 'STR',
				'usu_varApellido2' => 'STR',
				'usu_varNombreCompleto' => 'STR',
				'usu_varEmail' => 'STR',
				'usu_varNumeroCelular' => 'STR',
				'usu_varExtension' => 'STR',
				'usu_varPassword' => 'STR',
				'usu_varToken' => 'STR',
				'usu_varTokenContrasena' => 'STR',
				'usu_bolGetContrasena' => 'INT',
				'usu_bolEstadoUsuario' => 'INT',
				'usu_varImagen' => 'STR',
				'usu_intNumeroIntentos' => 'INT',
				'usu_dtimFechaCreacion' => 'STR',
				'usu_dtimFechaActualizacion' => 'STR',
				'usu_dtimFechaRecuperacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'rol_intIdRol' => 'INT',
				'car_intIdCargo' => 'INT',
				'cdo_intIdCategoriaDocumento' => 'INT'
			],

			'nullable'		=> ['usu_intId', 'usu_varNombre2', 'usu_varApellido2', 'usu_varNumeroCelular', 'usu_varExtension', 'usu_varToken', 'usu_varTokenContrasena', 'usu_bolGetContrasena', 'usu_bolEstadoUsuario', 'usu_varImagen', 'usu_intNumeroIntentos', 'usu_dtimFechaCreacion', 'usu_dtimFechaActualizacion', 'usu_dtimFechaRecuperacion', 'est_intIdEstado'],

			'rules' 		=> [
				'usu_intId' => ['type' => 'int'],
				'usu_varNroIdentificacion' => ['type' => 'str', 'max' => 50, 'required' => true],
				'usu_varNombre' => ['type' => 'str', 'max' => 50, 'required' => true],
				'usu_varNombre2' => ['type' => 'str', 'max' => 50],
				'usu_varApellido' => ['type' => 'str', 'max' => 50, 'required' => true],
				'usu_varApellido2' => ['type' => 'str', 'max' => 50],
				'usu_varNombreCompleto' => ['type' => 'str', 'max' => 100, 'required' => true],
				'usu_varEmail' => ['type' => 'str', 'max' => 50, 'required' => true],
				'usu_varNumeroCelular' => ['type' => 'str', 'max' => 20],
				'usu_varExtension' => ['type' => 'str', 'max' => 20],
				'usu_varPassword' => ['type' => 'str', 'max' => 64, 'required' => true],
				'usu_varToken' => ['type' => 'str', 'max' => 50],
				'usu_varTokenContrasena' => ['type' => 'str', 'max' => 100],
				'usu_bolGetContrasena' => ['type' => 'int'],
				'usu_bolEstadoUsuario' => ['type' => 'bool'],
				'usu_varImagen' => ['type' => 'str', 'max' => 250],
				'usu_intNumeroIntentos' => ['type' => 'int'],
				'usu_dtimFechaCreacion' => ['type' => 'datetime'],
				'usu_dtimFechaActualizacion' => ['type' => 'datetime'],
				'usu_dtimFechaRecuperacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'rol_intIdRol' => ['type' => 'int', 'required' => true],
				'car_intIdCargo' => ['type' => 'int', 'required' => true],
				'cdo_intIdCategoriaDocumento' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_cargo' => [
					['tbl_cargo.car_intId','tbl_usuario.car_intIdCargo']
				],
				'tbl_categoria_documento' => [
					['tbl_categoria_documento.cdo_intId','tbl_usuario.cdo_intIdCategoriaDocumento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_usuario.est_intIdEstado']
				],
				'tbl_rol' => [
					['tbl_rol.rol_intId','tbl_usuario.rol_intIdRol']
				]
			]
		];
	}	
}

