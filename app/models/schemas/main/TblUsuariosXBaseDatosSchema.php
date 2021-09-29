<?php

namespace simplerest\models\schemas\main;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblUsuariosXBaseDatosSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_usuarios_x_base_datos',

			'id_name'		=> 'usb_intIdUsuarioBase',

			'attr_types'	=> [
				'usb_intIdUsuarioBase' => 'INT',
				'bas_intIdBasedatos' => 'INT',
				'usu_intIdUsuario' => 'INT',
				'est_intIdEstado' => 'INT'
			],

			'nullable'		=> ['usb_intIdUsuarioBase', 'bas_intIdBasedatos', 'usu_intIdUsuario', 'est_intIdEstado'],

			'rules' 		=> [

			],

			'relationships' => [
				
			]
		];
	}	
}

