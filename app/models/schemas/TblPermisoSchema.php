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

			'nullable'		=> ['per_intId', 'per_dtimFechaCreacion', 'per_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'per_intId' => ['type' => 'int'],
				'per_varNombre' => ['type' => 'str', 'max' => 50, 'required' => true],
				'per_varDescripcion' => ['type' => 'str', 'max' => 100, 'required' => true],
				'per_dtimFechaCreacion' => ['type' => 'datetime'],
				'per_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_permiso.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_permiso.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_permiso.usu_intIdActualizador']
				],
				'tbl_rol_permiso' => [
					['tbl_rol_permiso.per_intIdPermiso','tbl_permiso.per_intId']
				]
			]
		];
	}	
}

