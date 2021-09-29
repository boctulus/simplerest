<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblEmpresaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_empresa',

			'id_name'		=> 'emp_intId',

			'attr_types'	=> [
				'emp_intId' => 'INT',
				'emp_varRazonSocial' => 'STR',
				'emp_varNit' => 'STR',
				'emp_varEmail' => 'STR',
				'emp_varCelular' => 'STR',
				'emp_varTipoCuenta' => 'STR',
				'emp_varNumeroCuenta' => 'STR',
				'emp_varPila' => 'STR',
				'emp_intAnoConstitucion' => 'INT',
				'emp_bolAplicarLey14292020' => 'INT',
				'emp_bolAplicarLey5902000' => 'INT',
				'emp_bolAportaParafiscales16072012' => 'INT',
				'emp_bolAplicaDecreto5582000' => 'INT',
				'emp_dtimFechaCreacion' => 'STR',
				'emp_dtimFechaActualizacion' => 'STR',
				'arl_intIdArl' => 'INT',
				'opp_intIdOperador' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['emp_intId', 'emp_intAnoConstitucion', 'emp_bolAplicarLey14292020', 'emp_bolAplicarLey5902000', 'emp_bolAportaParafiscales16072012', 'emp_bolAplicaDecreto5582000', 'emp_dtimFechaCreacion', 'emp_dtimFechaActualizacion', 'arl_intIdArl', 'opp_intIdOperador', 'est_intIdEstado'],

			'rules' 		=> [
				'emp_varRazonSocial' => ['max' => 300],
				'emp_varNit' => ['max' => 20],
				'emp_varEmail' => ['max' => 100],
				'emp_varCelular' => ['max' => 50],
				'emp_varTipoCuenta' => ['max' => 20],
				'emp_varNumeroCuenta' => ['max' => 50],
				'emp_varPila' => ['max' => 350]
			],

			'relationships' => [
				
			]
		];
	}	
}

