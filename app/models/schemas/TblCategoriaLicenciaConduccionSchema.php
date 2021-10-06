<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCategoriaLicenciaConduccionSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_categoria_licencia_conduccion',

			'id_name'		=> 'clc_intId',

			'attr_types'	=> [
				'clc_intId' => 'INT',
				'clc_varNombre' => 'STR',
				'clc_varDescripcion' => 'STR',
				'clc_dtimFechaCreacion' => 'STR',
				'clc_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['clc_intId', 'clc_dtimFechaCreacion', 'clc_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'clc_intId' => ['type' => 'int'],
				'clc_varNombre' => ['type' => 'str', 'max' => 50, 'required' => true],
				'clc_varDescripcion' => ['type' => 'str', 'max' => 250, 'required' => true],
				'clc_dtimFechaCreacion' => ['type' => 'datetime'],
				'clc_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_licencia_conduccion.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_categoria_licencia_conduccion.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_categoria_licencia_conduccion.usu_intIdCreador']
				]
			]
		];
	}	
}

