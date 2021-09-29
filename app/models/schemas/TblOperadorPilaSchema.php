<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblOperadorPilaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_operador_pila',

			'id_name'		=> 'opp_intId',

			'attr_types'	=> [
				'opp_intId' => 'INT',
				'opp_varCodigo' => 'STR',
				'opp_varDescripcion' => 'STR',
				'opp_dtimFechaCreacion' => 'STR',
				'opp_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['opp_intId', 'opp_dtimFechaCreacion', 'opp_dtimFechaActualizacion', 'est_intIdEstado'],

			'rules' 		=> [
				'opp_varCodigo' => ['max' => 50],
				'opp_varDescripcion' => ['max' => 300]
			],

			'relationships' => [
				
			]
		];
	}	
}

