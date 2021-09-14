<?php

namespace simplerest\models\schemas\main;

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
				'usu_varNroIdentificacion' => 'INT',
				'usu_varEmail' => 'STR',
				'usu_varPassword' => 'STR',
				'usu_varNombre' => 'STR',
				'usu_varApellido' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_dtimFechaActualizacion' => 'STR'
			],

			'nullable'		=> ['usu_intId'],

			'rules' 		=> [
				'usu_varEmail' => ['max' => 320],
				'usu_varPassword' => ['max' => 50],
				'usu_varNombre' => ['max' => 50],
				'usu_varApellido' => ['max' => 50]
			],

			'relationships' => [
				
			]
		];
	}	
}

