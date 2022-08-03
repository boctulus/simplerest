<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class AsignadoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'asignado',

			'id_name'		=> 'chr_asigcodigo',

			'attr_types'	=> [
				'chr_asigcodigo' => 'STR',
				'chr_sucucodigo' => 'STR',
				'chr_emplcodigo' => 'STR',
				'dtt_asigfechaalta' => 'STR',
				'dtt_asigfechabaja' => 'STR'
			],

			'primary'		=> ['chr_asigcodigo'],

			'autoincrement' => null,

			'nullable'		=> ['dtt_asigfechabaja'],

			'uniques'		=> [],

			'rules' 		=> [
				'chr_asigcodigo' => ['type' => 'str', 'required' => true],
				'chr_sucucodigo' => ['type' => 'str', 'required' => true],
				'chr_emplcodigo' => ['type' => 'str', 'required' => true],
				'dtt_asigfechaalta' => ['type' => 'date', 'required' => true],
				'dtt_asigfechabaja' => ['type' => 'date']
			],

			'fks' 			=> ['chr_emplcodigo', 'chr_sucucodigo'],

			'relationships' => [
				'empleado' => [
					['empleado.chr_emplcodigo','asignado.chr_emplcodigo']
				],
				'sucursal' => [
					['sucursal.chr_sucucodigo','asignado.chr_sucucodigo']
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
        0 => 'asignado',
        1 => 'chr_emplcodigo',
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
        0 => 'asignado',
        1 => 'chr_sucucodigo',
      ),
    ),
  ),
),

			'relationships_from' => [
				'empleado' => [
					['empleado.chr_emplcodigo','asignado.chr_emplcodigo']
				],
				'sucursal' => [
					['sucursal.chr_sucucodigo','asignado.chr_sucucodigo']
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
        0 => 'asignado',
        1 => 'chr_emplcodigo',
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
        0 => 'asignado',
        1 => 'chr_sucucodigo',
      ),
    ),
  ),
)
		];
	}	
}

