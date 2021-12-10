<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCiudadSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_ciudad',

			'id_name'		=> 'ciu_intId',

			'attr_types'	=> [
				'ciu_intId' => 'INT',
				'ciu_varCodigo' => 'STR',
				'ciu_varCiudad' => 'STR',
				'ciu_varIndicativoTelefono' => 'STR',
				'ciu_dtimFechaCreacion' => 'STR',
				'ciu_dtimFechaActualizacion' => 'STR',
				'est_intIdEstado' => 'INT',
				'pai_intIdPais' => 'INT',
				'dep_intIdDepartamento' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['ciu_intId', 'ciu_varCodigo'],

			'autoincrement' => 'ciu_intId',

			'nullable'		=> ['ciu_intId', 'ciu_dtimFechaCreacion', 'ciu_dtimFechaActualizacion', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'ciu_intId' => ['type' => 'int'],
				'ciu_varCodigo' => ['type' => 'str', 'max' => 5, 'required' => true],
				'ciu_varCiudad' => ['type' => 'str', 'max' => 100, 'required' => true],
				'ciu_varIndicativoTelefono' => ['type' => 'str', 'max' => 3, 'required' => true],
				'ciu_dtimFechaCreacion' => ['type' => 'datetime'],
				'ciu_dtimFechaActualizacion' => ['type' => 'datetime'],
				'est_intIdEstado' => ['type' => 'int'],
				'pai_intIdPais' => ['type' => 'int', 'required' => true],
				'dep_intIdDepartamento' => ['type' => 'int', 'required' => true],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['dep_intIdDepartamento', 'est_intIdEstado', 'pai_intIdPais', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_departamento' => [
					['tbl_departamento.dep_intId','tbl_ciudad.dep_intIdDepartamento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_ciudad.est_intIdEstado']
				],
				'tbl_pais' => [
					['tbl_pais.pai_intId','tbl_ciudad.pai_intIdPais']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_ciudad.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_ciudad.usu_intIdCreador']
				],
				'tbl_contacto' => [
					['tbl_contacto.ciu_intIdCiudad','tbl_ciudad.ciu_intId']
				],
				'tbl_persona' => [
					['tbl_persona.ciu_intIdCiudad','tbl_ciudad.ciu_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_departamento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_departamento',
				        1 => 'dep_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'dep_intIdDepartamento',
				      ),
				    ),
				  ),
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
				        0 => 'tbl_ciudad',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_pais' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pais',
				        1 => 'pai_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'pai_intIdPais',
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
				        0 => 'tbl_ciudad',
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
				        0 => 'tbl_ciudad',
				        1 => 'usu_intIdCreador',
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
				        1 => 'ciu_intIdCiudad',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'ciu_intId',
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
				        1 => 'ciu_intIdCiudad',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'ciu_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_departamento' => [
					['tbl_departamento.dep_intId','tbl_ciudad.dep_intIdDepartamento']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_ciudad.est_intIdEstado']
				],
				'tbl_pais' => [
					['tbl_pais.pai_intId','tbl_ciudad.pai_intIdPais']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_ciudad.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_ciudad.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_departamento' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_departamento',
				        1 => 'dep_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'dep_intIdDepartamento',
				      ),
				    ),
				  ),
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
				        0 => 'tbl_ciudad',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_pais' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_pais',
				        1 => 'pai_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_ciudad',
				        1 => 'pai_intIdPais',
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
				        0 => 'tbl_ciudad',
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
				        0 => 'tbl_ciudad',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

