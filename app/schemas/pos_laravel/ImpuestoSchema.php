<?php

namespace Boctulus\Simplerest\Schemas\pos_laravel;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class ImpuestoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'impuesto',

			'id_name'			=> 'idImpuesto',

			'fields'			=> ['idImpuesto', 'valor_porcentaje', 'nombre'],

			'attr_types'		=> [
				'idImpuesto' => 'INT',
				'valor_porcentaje' => 'STR',
				'nombre' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idImpuesto'],

			'autoincrement' 	=> 'idImpuesto',

			'nullable'			=> ['idImpuesto', 'valor_porcentaje', 'nombre'],

			'required'			=> [],

			'uniques'			=> [],

			'rules' 			=> [
				'idImpuesto' => ['type' => 'int'],
				'valor_porcentaje' => ['type' => 'decimal(10,2)'],
				'nombre' => ['type' => 'str', 'max' => 45]
			],

			'fks' 				=> [],

			'relationships' => [
				'venta' => [
					['venta.idImpuesto','impuesto.idImpuesto']
				]
			],

			'expanded_relationships' => array (
  'venta' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'venta',
        1 => 'idImpuesto',
      ),
      1 => 
      array (
        0 => 'impuesto',
        1 => 'idImpuesto',
      ),
    ),
  ),
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}

