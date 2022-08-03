<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class CuentaSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'cuenta',

			'id_name'		=> 'chr_cuencodigo',

			'attr_types'	=> [
				'chr_cuencodigo' => 'STR',
				'chr_monecodigo' => 'STR',
				'chr_sucucodigo' => 'STR',
				'chr_emplcreacuenta' => 'STR',
				'chr_cliecodigo' => 'STR',
				'dec_cuensaldo' => 'STR',
				'dtt_cuenfechacreacion' => 'STR',
				'vch_cuenestado' => 'STR',
				'int_cuencontmov' => 'INT',
				'chr_cuenclave' => 'STR'
			],

			'primary'		=> ['chr_cuencodigo'],

			'autoincrement' => null,

			'nullable'		=> ['vch_cuenestado'],

			'uniques'		=> [],

			'rules' 		=> [
				'chr_cuencodigo' => ['type' => 'str', 'required' => true],
				'chr_monecodigo' => ['type' => 'str', 'required' => true],
				'chr_sucucodigo' => ['type' => 'str', 'required' => true],
				'chr_emplcreacuenta' => ['type' => 'str', 'required' => true],
				'chr_cliecodigo' => ['type' => 'str', 'required' => true],
				'dec_cuensaldo' => ['type' => 'decimal(12,2)', 'required' => true],
				'dtt_cuenfechacreacion' => ['type' => 'date', 'required' => true],
				'vch_cuenestado' => ['type' => 'str', 'max' => 15],
				'int_cuencontmov' => ['type' => 'int', 'required' => true],
				'chr_cuenclave' => ['type' => 'str', 'required' => true]
			],

			'fks' 			=> ['chr_cliecodigo', 'chr_emplcreacuenta', 'chr_monecodigo', 'chr_sucucodigo'],

			'relationships' => [
				'cliente' => [
					['cliente.chr_cliecodigo','cuenta.chr_cliecodigo']
				],
				'empleado' => [
					['empleado.chr_emplcodigo','cuenta.chr_emplcreacuenta']
				],
				'moneda' => [
					['moneda.chr_monecodigo','cuenta.chr_monecodigo']
				],
				'sucursal' => [
					['sucursal.chr_sucucodigo','cuenta.chr_sucucodigo']
				],
				'movimiento' => [
					['movimiento.chr_cuencodigo','cuenta.chr_cuencodigo']
				]
			],

			'expanded_relationships' => array (
  'cliente' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'cliente',
        1 => 'chr_cliecodigo',
      ),
      1 => 
      array (
        0 => 'cuenta',
        1 => 'chr_cliecodigo',
      ),
    ),
  ),
  'empleado' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'empleado',
        1 => 'chr_emplcodigo',
      ),
      1 => 
      array (
        0 => 'cuenta',
        1 => 'chr_emplcreacuenta',
      ),
    ),
  ),
  'moneda' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'moneda',
        1 => 'chr_monecodigo',
      ),
      1 => 
      array (
        0 => 'cuenta',
        1 => 'chr_monecodigo',
      ),
    ),
  ),
  'sucursal' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'sucursal',
        1 => 'chr_sucucodigo',
      ),
      1 => 
      array (
        0 => 'cuenta',
        1 => 'chr_sucucodigo',
      ),
    ),
  ),
  'movimiento' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'movimiento',
        1 => 'chr_cuencodigo',
      ),
      1 => 
      array (
        0 => 'cuenta',
        1 => 'chr_cuencodigo',
      ),
    ),
  ),
),

			'relationships_from' => [
				'cliente' => [
					['cliente.chr_cliecodigo','cuenta.chr_cliecodigo']
				],
				'empleado' => [
					['empleado.chr_emplcodigo','cuenta.chr_emplcreacuenta']
				],
				'moneda' => [
					['moneda.chr_monecodigo','cuenta.chr_monecodigo']
				],
				'sucursal' => [
					['sucursal.chr_sucucodigo','cuenta.chr_sucucodigo']
				]
			],

			'expanded_relationships_from' => array (
  'cliente' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'cliente',
        1 => 'chr_cliecodigo',
      ),
      1 => 
      array (
        0 => 'cuenta',
        1 => 'chr_cliecodigo',
      ),
    ),
  ),
  'empleado' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'empleado',
        1 => 'chr_emplcodigo',
      ),
      1 => 
      array (
        0 => 'cuenta',
        1 => 'chr_emplcreacuenta',
      ),
    ),
  ),
  'moneda' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'moneda',
        1 => 'chr_monecodigo',
      ),
      1 => 
      array (
        0 => 'cuenta',
        1 => 'chr_monecodigo',
      ),
    ),
  ),
  'sucursal' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'sucursal',
        1 => 'chr_sucucodigo',
      ),
      1 => 
      array (
        0 => 'cuenta',
        1 => 'chr_sucucodigo',
      ),
    ),
  ),
)
		];
	}	
}

