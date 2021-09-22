<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblRolPermisoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_rol_permiso',

			'id_name'		=> 'rpe_intId',

			'attr_types'	=> [
				'rpe_intId' => 'INT',
				'rpe_dtimFechaCreacion' => 'STR',
				'rpe_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'rol_intIdRol' => 'INT',
				'per_intIdPermiso' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['rpe_intId'],

			'rules' 		=> [

			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_rol_permiso.est_intIdEstado']
				],
				'tbl_permiso' => [
					['tbl_permiso.per_intId','tbl_rol_permiso.per_intIdPermiso']
				],
				'tbl_rol' => [
					['tbl_rol.rol_intId','tbl_rol_permiso.rol_intIdRol']
				],
				'tbl_usuario' => [
					['usu_intIdActualizadors.usu_intId','tbl_rol_permiso.usu_intIdActualizador'],
					['usu_intIdActualizadorss.usu_intId','tbl_rol_permiso.usu_intIdCreador']
				]
			]
		];
	}	
}

