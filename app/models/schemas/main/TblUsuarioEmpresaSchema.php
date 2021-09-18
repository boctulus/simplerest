<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblUsuarioEmpresaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_usuario_empresa',

			'id_name'		=> 'use_intId',

			'attr_types'	=> [
				'use_intId' => 'INT',
				'use_varNombre' => 'STR',
				'use_varEmail' => 'STR',
				'use_varUsuario' => 'STR',
				'use_decPassword' => 'STR',
				'use_varTipo' => 'STR',
				'use_dtimFechaCreacion' => 'STR',
				'use_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['use_intId', 'use_varNombre', 'use_varTipo', 'use_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'rules' 		=> [
				'use_varNombre' => ['max' => 250],
				'use_varEmail' => ['max' => 100],
				'use_varUsuario' => ['max' => 250],
				'use_decPassword' => ['max' => 100],
				'use_varTipo' => ['max' => 100]
			],

			'relationships' => [
				'user_roles' => [ 'tbl_usuario_empresa.use_intId','user_roles.user_id' ]
			]
		];
	}	
}

