<?php

namespace simplerest\models\schemas;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblGrupoProductoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_grupo_producto',

			'id_name'		=> 'grp_intId',

			'attr_types'	=> [
				'grp_intId' => 'INT',
				'grp_varSiglaGrupoProducto' => 'STR',
				'grp_varDescripcionGrupo' => 'STR',
				'grp_dtimFechaCreacion' => 'STR',
				'grp_dtimFechaActualizacion' => 'STR',
				'grp_intConsecutivoGrupoProducto' => 'INT',
				'est_intIdEstado' => 'INT',
				'cap_intIdCategoriaProducto' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'nullable'		=> ['grp_intId', 'grp_dtimFechaCreacion', 'grp_dtimFechaActualizacion', 'grp_intConsecutivoGrupoProducto', 'est_intIdEstado', 'cap_intIdCategoriaProducto'],

			'rules' 		=> [
				'grp_intId' => ['type' => 'int'],
				'grp_varSiglaGrupoProducto' => ['type' => 'str', 'max' => 50, 'required' => true],
				'grp_varDescripcionGrupo' => ['type' => 'str', 'max' => 50, 'required' => true],
				'grp_dtimFechaCreacion' => ['type' => 'datetime'],
				'grp_dtimFechaActualizacion' => ['type' => 'datetime'],
				'grp_intConsecutivoGrupoProducto' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'cap_intIdCategoriaProducto' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'relationships' => [
				'tbl_categoria_producto' => [
					['tbl_categoria_producto.cap_intId','tbl_grupo_producto.cap_intIdCategoriaProducto']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_grupo_producto.est_intIdEstado']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_grupo_producto.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_grupo_producto.usu_intIdCreador']
				],
				'tbl_producto' => [
					['tbl_producto.grp_intIdGrupoProducto','tbl_grupo_producto.grp_intId']
				]
			]
		];
	}	
}
