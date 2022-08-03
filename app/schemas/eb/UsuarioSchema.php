<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class UsuarioSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'usuario',

			'id_name'		=> 'chr_emplcodigo',

			'attr_types'	=> [
				'chr_emplcodigo' => 'STR',
				'vch_emplusuario' => 'STR',
				'vch_emplclave' => 'STR',
				'vch_emplestado' => 'STR'
			],

			'primary'		=> ['chr_emplcodigo'],

			'autoincrement' => null,

			'nullable'		=> ['vch_emplestado'],

			'uniques'		=> ['vch_emplusuario'],

			'rules' 		=> [
				'chr_emplcodigo' => ['type' => 'str', 'required' => true],
				'vch_emplusuario' => ['type' => 'str', 'max' => 20, 'required' => true],
				'vch_emplclave' => ['type' => 'str', 'max' => 50, 'required' => true],
				'vch_emplestado' => ['type' => 'str', 'max' => 15]
			],

			'fks' 			=> ['chr_emplcodigo'],

			'relationships' => [
				'empleado' => [
					['empleado.chr_emplcodigo','usuario.chr_emplcodigo']
				],
				'permiso' => [
					['permiso.chr_emplcodigo','usuario.chr_emplcodigo']
				]
			],

			'expanded_relationships' => array (
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
        0 => 'usuario',
        1 => 'chr_emplcodigo',
      ),
    ),
  ),
  'permiso' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'permiso',
        1 => 'chr_emplcodigo',
      ),
      1 => 
      array (
        0 => 'usuario',
        1 => 'chr_emplcodigo',
      ),
    ),
  ),
),

			'relationships_from' => [
				'empleado' => [
					['empleado.chr_emplcodigo','usuario.chr_emplcodigo']
				]
			],

			'expanded_relationships_from' => array (
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
        0 => 'usuario',
        1 => 'chr_emplcodigo',
      ),
    ),
  ),
)
		];
	}	
}

