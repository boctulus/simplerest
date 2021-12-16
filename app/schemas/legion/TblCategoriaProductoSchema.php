<?php

namespace simplerest\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblCategoriaProductoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_categoria_producto',

			'id_name'		=> 'cap_intId',

			'attr_types'	=> [
				'cap_intId' => 'INT',
				'cap_varSiglaCategoriaProducto' => 'STR',
				'cap_varNombreCategoria' => 'STR',
				'cap_lonDescripcionCategoria' => 'STR',
				'cap_dtimFechaCreacion' => 'STR',
				'cap_dtimFechaActualizacion' => 'STR',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT',
				'est_intIdEstado' => 'INT'
			],

			'primary'		=> ['cap_intId'],

			'autoincrement' => 'cap_intId',

			'nullable'		=> ['cap_intId', 'cap_varSiglaCategoriaProducto', 'cap_lonDescripcionCategoria', 'cap_dtimFechaCreacion', 'cap_dtimFechaActualizacion', 'usu_intIdActualizador', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'cap_intId' => ['type' => 'int'],
				'cap_varSiglaCategoriaProducto' => ['type' => 'str', 'max' => 50],
				'cap_varNombreCategoria' => ['type' => 'str', 'max' => 50, 'required' => true],
				'cap_lonDescripcionCategoria' => ['type' => 'str'],
				'cap_dtimFechaCreacion' => ['type' => 'datetime'],
				'cap_dtimFechaActualizacion' => ['type' => 'datetime'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int']
			],

			'fks' 			=> ['est_intIdEstado', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_producto.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_categoria_producto.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_categoria_producto.usu_intIdCreador']
				],
				'tbl_producto' => [
					['tbl_producto.cap_intIdCategoriaProducto','tbl_categoria_producto.cap_intId']
				],
				'tbl_grupo_producto' => [
					['tbl_grupo_producto.cap_intIdCategoriaProducto','tbl_categoria_producto.cap_intId']
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
				        0 => 'tbl_categoria_producto',
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
				        0 => 'tbl_categoria_producto',
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
				        0 => 'tbl_categoria_producto',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				  'tbl_producto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_producto',
				        1 => 'cap_intIdCategoriaProducto',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_categoria_producto',
				        1 => 'cap_intId',
				      ),
				    ),
				  ),
				  'tbl_grupo_producto' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_grupo_producto',
				        1 => 'cap_intIdCategoriaProducto',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_categoria_producto',
				        1 => 'cap_intId',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_producto.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_categoria_producto.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_categoria_producto.usu_intIdCreador']
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
				        0 => 'tbl_categoria_producto',
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
				        0 => 'tbl_categoria_producto',
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
				        0 => 'tbl_categoria_producto',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

