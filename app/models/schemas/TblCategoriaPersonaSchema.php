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

			'nullable'		=> ['cap_intId'],

			'rules' 		=> [
				'cap_varCategoriaPersona' => ['max' => 100]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_persona.est_intIdEstado']
				]
			]
		];
	}	
}

