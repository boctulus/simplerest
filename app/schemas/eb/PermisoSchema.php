<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class PermisoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'permiso',

			'id_name'		=> 'int_moducodigo',

			'attr_types'	=> [
				'chr_emplcodigo' => 'STR',
				'int_moducodigo' => 'INT',
				'vch_permestado' => 'STR'
			],

			'primary'		=> ['chr_emplcodigo', 'int_moducodigo'],

			'autoincrement' => null,

			'nullable'		=> ['vch_permestado'],

			'uniques'		=> [],

			'rules' 		=> [
				'chr_emplcodigo' => ['type' => 'str', 'required' => true],
				'int_moducodigo' => ['type' => 'int', 'required' => true],
				'vch_permestado' => ['type' => 'str', 'max' => 15]
			],

			'fks' 			=> ['chr_emplcodigo', 'int_moducodigo'],

			'relationships' => [
				'usuario' => [
					['usuario.chr_emplcodigo','permiso.chr_emplcodigo']
				],
				'modulo' => [
					['modulo.int_moducodigo','permiso.int_moducodigo']
				]
			],

			'expanded_relationships' => array (
  'usuario' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'usuario',
        1 => 'chr_emplcodigo',
      ),
      1 => 
      array (
        0 => 'permiso',
        1 => 'chr_emplcodigo',
      ),
    ),
  ),
  'modulo' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'modulo',
        1 => 'int_moducodigo',
      ),
      1 => 
      array (
        0 => 'permiso',
        1 => 'int_moducodigo',
      ),
    ),
  ),
),

			'relationships_from' => [
				'usuario' => [
					['usuario.chr_emplcodigo','permiso.chr_emplcodigo']
				],
				'modulo' => [
					['modulo.int_moducodigo','permiso.int_moducodigo']
				]
			],

			'expanded_relationships_from' => array (
  'usuario' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'usuario',
        1 => 'chr_emplcodigo',
      ),
      1 => 
      array (
        0 => 'permiso',
        1 => 'chr_emplcodigo',
      ),
    ),
  ),
  'modulo' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'modulo',
        1 => 'int_moducodigo',
      ),
      1 => 
      array (
        0 => 'permiso',
        1 => 'int_moducodigo',
      ),
    ),
  ),
)
		];
	}	
}

