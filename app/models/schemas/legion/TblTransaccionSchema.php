<?php

namespace simplerest\models\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblTransaccionSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_transaccion',

			'id_name'		=> 'tra_intId',

			'attr_types'	=> [
				'tra_intId' => 'INT',
				'tra_varTransaccion' => 'STR',
				'tra_bolEstado' => 'INT',
				'tra_dtimFechaActualizacion' => 'STR',
				'tra_dtimFechaCreacion' => 'STR',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['tra_intId'],

			'autoincrement' => 'tra_intId',

			'nullable'		=> ['tra_intId', 'tra_dtimFechaActualizacion', 'tra_dtimFechaCreacion'],

			'uniques'		=> ['tra_varTransaccion'],

			'rules' 		=> [
				'tra_intId' => ['type' => 'int'],
				'tra_varTransaccion' => ['type' => 'str', 'max' => 25, 'required' => true],
				'tra_bolEstado' => ['type' => 'bool', 'required' => true],
				'tra_dtimFechaActualizacion' => ['type' => 'datetime'],
				'tra_dtimFechaCreacion' => ['type' => 'datetime'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_transaccion.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_transaccion.usu_intIdCreador']
				],
				'tbl_documento' => [
					['tbl_documento.tra_intIdTransaccion','tbl_transaccion.tra_intId']
				]
			],

			'expanded_relationships' => array (
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
				        0 => 'tbl_transaccion',
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
				        0 => 'tbl_transaccion',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_documento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_documento',
				        1 => 'tra_intIdTransaccion',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_transaccion',
				        1 => 'tra_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_transaccion.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_transaccion.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
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
				        0 => 'tbl_transaccion',
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
				        0 => 'tbl_transaccion',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

