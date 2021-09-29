<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblPermisoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_permiso',

			'id_name'		=> 'per_intId',

			'attr_types'	=> [
				'per_intId' => 'INT',
				'per_varNombre' => 'STR',
				'per_varDescripcion' => 'STR',
				'per_dtimFechaCreacion' => 'STR',
				'per_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['per_intId'],

			'rules' 		=> [
				'per_varNombre' => ['max' => 50],
				'per_varDescripcion' => ['max' => 100]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_permiso.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_permiso.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_permiso.usu_intIdCreador']
				],
				'tbl_rol_permiso' => [
					['tbl_rol_permiso.per_intIdPermiso','tbl_permiso.per_intId']
				]
			]
		];
	}	
}

