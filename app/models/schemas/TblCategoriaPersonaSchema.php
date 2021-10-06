<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCategoriaPersonaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_categoria_persona',

			'id_name'		=> 'cap_intId',

			'attr_types'	=> [
				'cap_intId' => 'INT',
				'cap_varCategoriaPersona' => 'STR',
				'cap_dtimFechaCreacion' => 'STR',
				'cap_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['cap_intId', 'cap_dtimFechaCreacion', 'cap_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'cap_intId' => ['type' => 'int'],
				'cap_varCategoriaPersona' => ['type' => 'str', 'max' => 100, 'required' => true],
				'cap_dtimFechaCreacion' => ['type' => 'datetime'],
				'cap_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_persona.est_intIdEstado']
				]
			]
		];
	}	
}

