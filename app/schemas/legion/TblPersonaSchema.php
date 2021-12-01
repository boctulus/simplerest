<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblPersonaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_persona',

			'id_name'		=> 'per_intId',

			'attr_types'	=> [
				'per_intId' => 'INT',
				'per_varIdentificacion' => 'STR',
				'per_varDV' => 'STR',
				'per_varRazonSocial' => 'STR',
				'per_varNombre' => 'STR',
				'per_varNombre2' => 'STR',
				'per_varApellido' => 'STR',
				'per_varApellido2' => 'STR',
				'per_varNombreCompleto' => 'STR',
				'per_varDireccion' => 'STR',
				'per_varCelular' => 'STR',
				'per_varTelefono' => 'STR',
				'per_varEmail' => 'STR',
				'per_datFechaNacimiento' => 'STR',
				'per_dtimFechaCreacion' => 'STR',
				'per_dtimFechaActualizacion' => 'STR',
				'tpr_intIdTipoPersona' => 'INT',
				'pai_intIdPais' => 'INT',
				'ciu_intIdCiudad' => 'INT',
				'gen_intIdGenero' => 'INT',
				'cid_intIdCategoriIdentificacion' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['per_intId'],

			'autoincrement' => 'per_intId',

			'nullable'		=> ['per_intId', 'per_varRazonSocial', 'per_varNombre', 'per_varNombre2', 'per_varApellido', 'per_varApellido2', 'per_varTelefono', 'per_dtimFechaCreacion', 'per_dtimFechaActualizacion', 'tpr_intIdTipoPersona', 'gen_intIdGenero', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'per_intId' => ['type' => 'int'],
				'per_varIdentificacion' => ['type' => 'str', 'max' => 20, 'required' => true],
				'per_varDV' => ['type' => 'str', 'max' => 1, 'required' => true],
				'per_varRazonSocial' => ['type' => 'str', 'max' => 200],
				'per_varNombre' => ['type' => 'str', 'max' => 100],
				'per_varNombre2' => ['type' => 'str', 'max' => 100],
				'per_varApellido' => ['type' => 'str', 'max' => 100],
				'per_varApellido2' => ['type' => 'str', 'max' => 100],
				'per_varNombreCompleto' => ['type' => 'str', 'required' => true],
				'per_varDireccion' => ['type' => 'str', 'max' => 255, 'required' => true],
				'per_varCelular' => ['type' => 'str', 'max' => 15, 'required' => true],
				'per_varTelefono' => ['type' => 'str', 'max' => 15],
				'per_varEmail' => ['type' => 'str', 'max' => 100, 'required' => true],
				'per_datFechaNacimiento' => ['type' => 'date', 'required' => true],
				'per_dtimFechaCreacion' => ['type' => 'datetime'],
				'per_dtimFechaActualizacion' => ['type' => 'datetime'],
				'tpr_intIdTipoPersona' => ['type' => 'int'],
				'pai_intIdPais' => ['type' => 'int', 'required' => true],
				'ciu_intIdCiudad' => ['type' => 'int', 'required' => true],
				'gen_intIdGenero' => ['type' => 'int'],
				'cid_intIdCategoriIdentificacion' => ['type' => 'int', 'required' => true],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['cid_intIdCategoriIdentificacion', 'ciu_intIdCiudad', 'est_intIdEstado', 'gen_intIdGenero', 'pai_intIdPais', 'tpr_intIdTipoPersona', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_categoria_identificacion' => [
					['tbl_categoria_identificacion.cid_intId','tbl_persona.cid_intIdCategoriIdentificacion']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.ciu_intId','tbl_persona.ciu_intIdCiudad']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_persona.est_intIdEstado']
				],
				'tbl_genero' => [
					['tbl_genero.gen_intId','tbl_persona.gen_intIdGenero']
				],
				'tbl_pais' => [
					['tbl_pais.pai_intId','tbl_persona.pai_intIdPais']
				],
				'tbl_tipo_persona' => [
					['tbl_tipo_persona.tpr_intId','tbl_persona.tpr_intIdTipoPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_persona.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_persona.usu_intIdCreador']
				],
				'tbl_proveedor' => [
					['tbl_proveedor.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_cliente' => [
					['tbl_cliente.ali_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_factura' => [
					['tbl_factura.per_intIdPersona','tbl_persona.per_intId']
				],
				'tbl_categoria_persona_persona' => [
					['tbl_categoria_persona_persona.per_intIdPersona','tbl_persona.per_intId']
				]
			],

			'expanded_relationships' => array (
				  'tbl_categoria_identificacion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_identificacion',
				        1 => 'cid_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'cid_intIdCategoriIdentificacion',
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
				        1 => 'ciu_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'ciu_intIdCiudad',
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
				        0 => 'tbl_persona',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_genero' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_genero',
				        1 => 'gen_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'gen_intIdGenero',
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
				        0 => 'tbl_persona',
				        1 => 'pai_intIdPais',
				      ),
				    ),
				  ),
				  'tbl_tipo_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_persona',
				        1 => 'tpr_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'tpr_intIdTipoPersona',
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
				        0 => 'tbl_persona',
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
				        0 => 'tbl_persona',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_factura' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_factura',
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
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
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				    ),
				  ),
				  'tbl_proveedor' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_proveedor',
				        1 => 'per_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				    ),
				  ),
				  'tbl_cliente' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'ali_intIdPersona',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'per_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_categoria_identificacion' => [
					['tbl_categoria_identificacion.cid_intId','tbl_persona.cid_intIdCategoriIdentificacion']
				],
				'tbl_ciudad' => [
					['tbl_ciudad.ciu_intId','tbl_persona.ciu_intIdCiudad']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_persona.est_intIdEstado']
				],
				'tbl_genero' => [
					['tbl_genero.gen_intId','tbl_persona.gen_intIdGenero']
				],
				'tbl_pais' => [
					['tbl_pais.pai_intId','tbl_persona.pai_intIdPais']
				],
				'tbl_tipo_persona' => [
					['tbl_tipo_persona.tpr_intId','tbl_persona.tpr_intIdTipoPersona']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_persona.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_persona.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_categoria_identificacion' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_categoria_identificacion',
				        1 => 'cid_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'cid_intIdCategoriIdentificacion',
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
				        1 => 'ciu_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'ciu_intIdCiudad',
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
				        0 => 'tbl_persona',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_genero' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_genero',
				        1 => 'gen_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'gen_intIdGenero',
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
				        0 => 'tbl_persona',
				        1 => 'pai_intIdPais',
				      ),
				    ),
				  ),
				  'tbl_tipo_persona' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_tipo_persona',
				        1 => 'tpr_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_persona',
				        1 => 'tpr_intIdTipoPersona',
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
				        0 => 'tbl_persona',
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
				        0 => 'tbl_persona',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

