<?php

namespace Boctulus\Simplerest\Schemas\main;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

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

			'primary'		=> ['usb_intIdUsuarioBase'],

			'autoincrement' => 'usb_intIdUsuarioBase',

			'nullable'		=> ['usb_intIdUsuarioBase', 'bas_intIdBasedatos', 'usu_intIdUsuario', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'usb_intIdUsuarioBase' => ['type' => 'int'],
				'bas_intIdBasedatos' => ['type' => 'int'],
				'usu_intIdUsuario' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int']
			],

			'fks' 			=> [],

			'relationships' => [
				
			],

			'expanded_relationships' => array (
				),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
				)
		];
	}	
}

