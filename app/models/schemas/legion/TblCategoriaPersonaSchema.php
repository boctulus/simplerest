<?php

namespace simplerest\models\schemas\legion;

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

			'primary'		=> ['cap_intId'],

			'autoincrement' => 'cap_intId',

			'nullable'		=> ['cap_intId', 'cap_dtimFechaCreacion', 'cap_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> ['cap_varCategoriaPersona'],

			'rules' 		=> [
				'cap_intId' => ['type' => 'int'],
				'cap_varCategoriaPersona' => ['type' => 'str', 'max' => 100, 'required' => true],
				'cap_dtimFechaCreacion' => ['type' => 'datetime'],
				'cap_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_persona.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_categoria_persona.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_categoria_persona.usu_intIdCreador']
				],
				'tbl_categoria_persona_persona' => [
					['tbl_categoria_persona_persona.cap_intIdCategoriaPersona','tbl_categoria_persona.cap_intId']
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
				        0 => 'tbl_categoria_persona',
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
				        0 => 'tbl_categoria_persona',
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
				        0 => 'tbl_categoria_persona',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_categoria_persona_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_persona_persona',
				        1 => 'cap_intIdCategoriaPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_categoria_persona',
				        1 => 'cap_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_persona.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_categoria_persona.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_categoria_persona.usu_intIdCreador']
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
				        0 => 'tbl_categoria_persona',
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
				        0 => 'tbl_categoria_persona',
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
				        0 => 'tbl_categoria_persona',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

