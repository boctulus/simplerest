<?php

namespace simplerest\models\schemas\legion;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TblClienteInformacionTributariaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'tbl_cliente_informacion_tributaria',

			'id_name'		=> 'tic_intId',

			'attr_types'	=> [
				'tic_intId' => 'INT',
				'tic_intGranContribuyente' => 'INT',
				'tic_intllevarContabilidad' => 'INT',
				'tic_intCalcularIca' => 'INT',
				'tic_dtimFechaCreacion' => 'STR',
				'tic_dtimFechaActualizacion' => 'STR',
				'sub_intIdSubcuentacontable' => 'INT',
				'cli_intIdCliente' => 'INT',
				'est_intIdEstado' => 'INT',
				'usu_intIdCreador' => 'INT',
				'usu_intIdActualizador' => 'INT'
			],

			'primary'		=> ['tic_intId'],

			'autoincrement' => 'tic_intId',

			'nullable'		=> ['tic_intId', 'tic_dtimFechaCreacion', 'tic_dtimFechaActualizacion', 'sub_intIdSubcuentacontable', 'cli_intIdCliente', 'est_intIdEstado'],

			'uniques'		=> [],

			'rules' 		=> [
				'tic_intId' => ['type' => 'int'],
				'tic_intGranContribuyente' => ['type' => 'int', 'required' => true],
				'tic_intllevarContabilidad' => ['type' => 'int', 'required' => true],
				'tic_intCalcularIca' => ['type' => 'int', 'required' => true],
				'tic_dtimFechaCreacion' => ['type' => 'datetime'],
				'tic_dtimFechaActualizacion' => ['type' => 'datetime'],
				'sub_intIdSubcuentacontable' => ['type' => 'int'],
				'cli_intIdCliente' => ['type' => 'int'],
				'est_intIdEstado' => ['type' => 'int'],
				'usu_intIdCreador' => ['type' => 'int', 'required' => true],
				'usu_intIdActualizador' => ['type' => 'int', 'required' => true]
			],

			'fks' 			=> ['cli_intIdCliente', 'cli_intIdCliente', 'est_intIdEstado', 'sub_intIdSubcuentacontable', 'usu_intIdActualizador', 'usu_intIdCreador'],

			'relationships' => [
				'tbl_cliente' => [
					['tbl_cliente|__cli_intIdCliente.cli_intId','tbl_cliente_informacion_tributaria.cli_intIdCliente'],
					['tbl_cliente|__cli_intIdCliente.cli_intId','tbl_cliente_informacion_tributaria.cli_intIdCliente']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_cliente_informacion_tributaria.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_cliente_informacion_tributaria.sub_intIdSubcuentacontable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cliente_informacion_tributaria.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cliente_informacion_tributaria.usu_intIdCreador']
				]
			],

			'expanded_relationships' => array (
				  'tbl_cliente' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'cli_intId',
				        'alias' => '__cli_intIdCliente',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_informacion_tributaria',
				        1 => 'cli_intIdCliente',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'cli_intId',
				        'alias' => '__cli_intIdCliente',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_informacion_tributaria',
				        1 => 'cli_intIdCliente',
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
				        0 => 'tbl_cliente_informacion_tributaria',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_sub_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_informacion_tributaria',
				        1 => 'sub_intIdSubcuentacontable',
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
				        0 => 'tbl_cliente_informacion_tributaria',
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
				        0 => 'tbl_cliente_informacion_tributaria',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				),

			'relationships_from' => [
				'tbl_cliente' => [
					['tbl_cliente|__cli_intIdCliente.cli_intId','tbl_cliente_informacion_tributaria.cli_intIdCliente'],
					['tbl_cliente|__cli_intIdCliente.cli_intId','tbl_cliente_informacion_tributaria.cli_intIdCliente']
				],
				'tbl_estado' => [
					['tbl_estado.est_intId','tbl_cliente_informacion_tributaria.est_intIdEstado']
				],
				'tbl_sub_cuenta_contable' => [
					['tbl_sub_cuenta_contable.sub_intId','tbl_cliente_informacion_tributaria.sub_intIdSubcuentacontable']
				],
				'tbl_usuario' => [
					['tbl_usuario|__usu_intIdActualizador.usu_intId','tbl_cliente_informacion_tributaria.usu_intIdActualizador'],
					['tbl_usuario|__usu_intIdCreador.usu_intId','tbl_cliente_informacion_tributaria.usu_intIdCreador']
				]
			],

			'expanded_relationships_from' => array (
				  'tbl_cliente' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'cli_intId',
				        'alias' => '__cli_intIdCliente',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_informacion_tributaria',
				        1 => 'cli_intIdCliente',
				      ),
				    ),
				    1 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_cliente',
				        1 => 'cli_intId',
				        'alias' => '__cli_intIdCliente',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_informacion_tributaria',
				        1 => 'cli_intIdCliente',
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
				        0 => 'tbl_cliente_informacion_tributaria',
				        1 => 'est_intIdEstado',
				      ),
				    ),
				  ),
				  'tbl_sub_cuenta_contable' => 
				  array (
				    0 => 
				    array (
				      0 => 
				      array (
				        0 => 'tbl_sub_cuenta_contable',
				        1 => 'sub_intId',
				      ),
				      1 => 
				      array (
				        0 => 'tbl_cliente_informacion_tributaria',
				        1 => 'sub_intIdSubcuentacontable',
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
				        0 => 'tbl_cliente_informacion_tributaria',
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
				        0 => 'tbl_cliente_informacion_tributaria',
				        1 => 'usu_intIdCreador',
				      ),
				    ),
				  ),
				)
		];
	}	
}

