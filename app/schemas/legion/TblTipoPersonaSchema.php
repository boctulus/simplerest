<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblTipoPersonaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_tipo_persona',

			'id_name'		=> 'tpr_intId',

			'attr_types'	=> [
				'tpr_intId' => 'INT',
				'tpr_varCodigo' => 'STR',
				'tpr_varNombre' => 'STR',
				'tpr_lonDescripcion' => 'STR',
				'tpr_varCodigoDian' => 'STR',
				'tpr_dtimFechaCreacion' => 'STR',
				'tpr_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['tpr_intId'],

			'autoincrement' => 'tpr_intId',

			'nullable'		=> ['tpr_intId', 'tpr_dtimFechaCreacion', 'tpr_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'tpr_intId' => ['type' => 'int'],
				'tpr_varCodigo' => ['type' => 'str', 'max' => 100, 'required' => true],
				'tpr_varNombre' => ['type' => 'str', 'max' => 100, 'required' => true],
				'tpr_lonDescripcion' => ['type' => 'str', 'required' => true],
				'tpr_varCodigoDian' => ['type' => 'str', 'max' => 2, 'required' => true],
				'tpr_dtimFechaCreacion' => ['type' => 'datetime'],
				'tpr_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdCreador', 'usu_intIdActualizador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_tipo_persona.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_tipo_persona.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_tipo_persona.usu_intIdActualizador']
				],
				'tbl_persona' => [
					['tbl_persona.tpr_intIdTipoPersona','tbl_tipo_persona.tpr_intId']
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
				        0 => 'tbl_tipo_persona',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_tipo_persona',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_tipo_persona',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				  'tbl_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'tpr_intIdTipoPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_tipo_persona',
				        1 => 'tpr_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_tipo_persona.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_tipo_persona.usu_intIdCreador'],
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_tipo_persona.usu_intIdActualizador']
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
				        0 => 'tbl_tipo_persona',
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
				        'alias' => '__usu_intIdCreador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_tipo_persona',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_usuario',
				        1 => 'usu_intId',
				        'alias' => '__usu_intIdActualizador',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_tipo_persona',
				        1 => 'usu_intIdActualizador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

