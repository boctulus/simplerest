<?php

namespace simplerest\schemas\eb;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class LogsessionSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'logsession',

			'id_name'		=> 'ID',

			'attr_types'	=> [
				'ID' => 'INT',
				'chr_emplcodigo' => 'STR',
				'fec_ingreso' => 'STR',
				'fec_salida' => 'STR',
				'ip' => 'STR',
				'hostname' => 'STR'
			],

			'primary'		=> ['ID'],

			'autoincrement' => 'ID',

			'nullable'		=> ['ID', 'fec_salida', 'ip', 'hostname'],

			'uniques'		=> [],

			'rules' 		=> [
				'ID' => ['type' => 'int'],
				'chr_emplcodigo' => ['type' => 'str', 'required' => true],
				'fec_ingreso' => ['type' => 'datetime', 'required' => true],
				'fec_salida' => ['type' => 'datetime'],
				'ip' => ['type' => 'str', 'max' => 20],
				'hostname' => ['type' => 'str', 'max' => 100]
			],

			'fks' 			=> ['chr_emplcodigo'],

			'relationships' => [
				'empleado' => [
					['empleado.chr_emplcodigo','logsession.chr_emplcodigo']
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
        0 => 'logsession',
        1 => 'chr_emplcodigo',
      ),
    ),
  ),
),

			'relationships_from' => [
				'empleado' => [
					['empleado.chr_emplcodigo','logsession.chr_emplcodigo']
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
        0 => 'logsession',
        1 => 'chr_emplcodigo',
      ),
    ),
  ),
)
		];
	}	
}

