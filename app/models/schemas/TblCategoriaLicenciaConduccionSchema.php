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

			'nullable'		=> ['clc_intId'],

			'rules' 		=> [
				'clc_varNombre' => ['max' => 50],
				'clc_varDescripcion' => ['max' => 250]
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

