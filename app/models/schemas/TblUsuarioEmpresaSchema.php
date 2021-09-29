<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblUsuarioEmpresaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_usuario_empresa',

			'id_name'		=> 'uem_intId',

			'attr_types'	=> [
				'uem_intId' => 'INT',
				'uem_dtimFechaCreacion' => 'STR',
				'uem_dtimFechaActualizacion' => 'STR',
				'usu_intIdUsuario' => 'INT',
				'emp_intIdempresa' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['uem_intId', 'uem_dtimFechaCreacion', 'uem_dtimFechaActualizacion'],

			'rules' 		=> [

			],

			'relationships' => [
				
			]
		];
	}	
}

