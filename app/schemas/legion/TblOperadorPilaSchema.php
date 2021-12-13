<?php

namespace simplerest\schemas\legion;

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
				'opp_varNombre' => 'STR',
				'opp_lonDescripcion' => 'STR',
				'opp_dtimFechaCreacion' => 'STR',
				'opp_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['opp_intId'],

			'autoincrement' => 'opp_intId',

			'nullable'		=> ['opp_intId', 'opp_dtimFechaCreacion', 'opp_dtimFechaActualizacion', 'est_intIdEstado', 'usu_intIdActualizador'],

			'uniques'		=> [],

			'rules' 		=> [
				'opp_intId' => ['type' => 'int'],
				'opp_varCodigo' => ['type' => 'str', 'max' => 50, 'required' => true],
				'opp_varNombre' => ['type' => 'str', 'max' => 150, 'required' => true],
				'opp_lonDescripcion' => ['type' => 'str', 'required' => true],
				'opp_dtimFechaCreacion' => ['type' => 'datetime'],
				'opp_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int']
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_operador_pila.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_operador_pila.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_operador_pila.usu_intIdCreador']
				],
				'tbl_empresa_nomina' => [
					['tbl_empresa_nomina.opp_intIdOperador','tbl_operador_pila.opp_intId']
				],
				'tbl_empresa' => [
					['tbl_empresa.opp_intIdOperador','tbl_operador_pila.opp_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_estado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_operador_pila',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_usuario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_operador_pila',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_operador_pila',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_empresa_nomina' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa_nomina',
				        1 => 'opp_intIdOperador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_operador_pila',
				        1 => 'opp_intId',
				      ),
				    ),
				  ),
				  'tbl_empresa' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_empresa',
				        1 => 'opp_intIdOperador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_operador_pila',
				        1 => 'opp_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_operador_pila.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_operador_pila.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_operador_pila.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_estado' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_estado',
				        1 => 'est_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_operador_pila',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_usuario' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_operador_pila',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_operador_pila',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

