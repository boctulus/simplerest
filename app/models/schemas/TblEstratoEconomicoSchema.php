<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEstratoEconomicoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_estrato_economico',

			'id_name'		=> 'tec_intId',

			'attr_types'	=> [
				'tec_intId' => 'INT',
				'tec_varCodigo' => 'STR',
				'tec_varDescripcion' => 'STR',
				'tec_dtimFechaCreacion' => 'STR',
				'tec_dtimFechaActualizacion' => 'STR',
				'est_intIdestado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['tec_intId', 'tec_dtimFechaCreacion', 'tec_dtimFechaActualizacion', 'est_intIdestado'],

			'rules' 		=> [
				'tec_varCodigo' => ['max' => 20],
				'tec_varDescripcion' => ['max' => 250]
			],

			'relationships' => [
				
			]
		];
	}	
}

