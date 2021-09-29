<?php

namespace simplerest\models\schemas;

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
				'cap_varDescripcionCategoria' => 'STR',
				'cap_dtimFechaCreacion' => 'STR',
				'cap_dtimFechaActualizacion' => 'STR',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT',
				'est_intIdEstado_cap' => 'INT'
			],

			'nullable'		=> ['cap_intId', 'est_intIdEstado_cap'],

			'rules' 		=> [
				'cap_varSiglaCategoriaProducto' => ['max' => 50],
				'cap_varDescripcionCategoria' => ['max' => 50]
			],

			'relationships' => [
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_categoria_producto.est_intIdEstado_cap']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_categoria_producto.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_categoria_producto.usu_intIdCreador']
				],
				'tbl_grupo_producto' => [
					['tbl_grupo_producto.cap_intIdCategoriaProducto','tbl_categoria_producto.cap_intId']
				],
				'tbl_producto' => [
					['tbl_producto.cap_intIdCategoriaProducto','tbl_categoria_producto.cap_intId']
				]
			]
		];
	}	
}

