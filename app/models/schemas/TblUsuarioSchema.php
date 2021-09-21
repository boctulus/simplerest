<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblUsuarioSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_usuario',

			'id_name'		=> NULL,

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

			'nullable'		=> ['usu_varNombre', 'usu_varNombre2', 'usu_varApellido', 'usu_varApellido2', 'usu_varNombreCompleto', 'usu_varNumeroCelular', 'usu_varExtension', 'usu_dtimFechaCreacion', 'usu_dtimFechaActualizacion', 'usu_dtimFechaRecuperacion', 'cdo_intIdCategoriaDocumento'],

			'rules' 		=> [
				'usu_varNroIdentificacion' => ['max' => 50],
				'usu_varNombre' => ['max' => 50],
				'usu_varNombre2' => ['max' => 50],
				'usu_varApellido' => ['max' => 50],
				'usu_varApellido2' => ['max' => 50],
				'usu_varNombreCompleto' => ['max' => 100],
				'usu_varEmail' => ['max' => 50],
				'usu_varNumeroCelular' => ['max' => 20],
				'usu_varExtension' => ['max' => 20],
				'usu_varPassword' => ['max' => 64],
				'usu_varToken' => ['max' => 50],
				'usu_varTokenContrasena' => ['max' => 100],
				'usu_varImagen' => ['max' => 250]
			],

			'relationships' => [
				
			]
		];
	}	
}

