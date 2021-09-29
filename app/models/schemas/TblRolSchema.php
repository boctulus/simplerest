<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblRolSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_rol',

			'id_name'		=> 'rol_intId',

			'attr_types'	=> [
				'rol_intId' => 'INT',
				'rol_varNombre' => 'STR',
				'rol_varDescripcion' => 'STR',
				'rol_dtimFechaCreacion' => 'STR',
				'rol_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado_rol' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['rol_intId'],

			'rules' 		=> [
				'rol_varNombre' => ['max' => 50],
				'rol_varDescripcion' => ['max' => 100]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_rol.est_intIdEstado_rol']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_rol.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_rol.usu_intIdCreador'],
					['tbl_usuario.rol_intIdRol','tbl_rol.rol_intId']
				],
				'tbl_rol_permiso' => [
					['tbl_rol_permiso.rol_intIdRol','tbl_rol.rol_intId']
				]
			]
		];
	}	
}

