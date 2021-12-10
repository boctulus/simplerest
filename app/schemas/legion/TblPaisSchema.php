<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblPaisSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_pais',

			'id_name'		=> 'pai_intId',

			'attr_types'	=> [
				'pai_intId' => 'INT',
				'pai_varCodigo' => 'STR',
				'pai_varPais' => 'STR',
				'pai_varCodigoPaisCelular' => 'STR',
				'pai_dtimFechaCreacion' => 'STR',
				'pai_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'pai_intIdMoneda' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['pai_intId', 'pai_varCodigo'],

			'autoincrement' => 'pai_intId',

			'nullable'		=> ['pai_intId', 'pai_dtimFechaCreacion', 'pai_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'pai_intId' => ['type' => 'int'],
				'pai_varCodigo' => ['type' => 'str', 'max' => 4, 'required' => true],
				'pai_varPais' => ['type' => 'str', 'max' => 100, 'required' => true],
				'pai_varCodigoPaisCelular' => ['type' => 'str', 'max' => 3, 'required' => true],
				'pai_dtimFechaCreacion' => ['type' => 'datetime'],
				'pai_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'pai_intIdMoneda' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['est_intIdEstado', 'pai_intIdMoneda', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_pais.est_intIdEstado']
				],
				'tbl_moneda' => [
					['tbl_moneda.mon_intId','tbl_pais.pai_intIdMoneda']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_pais.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_pais.usu_intIdCreador']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.pai_intIdPais','tbl_pais.pai_intId']
				],
				'tbl_persona' => [
					['tbl_persona.pai_intIdPais','tbl_pais.pai_intId']
				],
				'tbl_departamento' => [
					['tbl_departamento.pai_intIdPais','tbl_pais.pai_intId']
				],
				'tbl_contacto' => [
					['tbl_contacto.pai_intIdPais','tbl_pais.pai_intId']
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
				        0 => 'tbl_pais',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_moneda' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_moneda',
				        1 => 'mon_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pais',
				        1 => 'pai_intIdMoneda',
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
				        0 => 'tbl_pais',
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
				        0 => 'tbl_pais',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_departamento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_departamento',
				        1 => 'pai_intIdPais',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pais',
				        1 => 'pai_intId',
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
				        1 => 'pai_intIdPais',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pais',
				        1 => 'pai_intId',
				      ),
				    ),
				  ),
				  'tbl_contacto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_contacto',
				        1 => 'pai_intIdPais',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pais',
				        1 => 'pai_intId',
				      ),
				    ),
				  ),
				  'tbl_ciudad' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'pai_intIdPais',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pais',
				        1 => 'pai_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_pais.est_intIdEstado']
				],
				'tbl_moneda' => [
					['tbl_moneda.mon_intId','tbl_pais.pai_intIdMoneda']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_pais.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_pais.usu_intIdCreador']
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
				        0 => 'tbl_pais',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_moneda' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_moneda',
				        1 => 'mon_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_pais',
				        1 => 'pai_intIdMoneda',
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
				        0 => 'tbl_pais',
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
				        0 => 'tbl_pais',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

