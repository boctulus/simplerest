<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class DocumentodteSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'documentodte',

			'id_name'			=> 'idDocumentoDte',

			'fields'			=> ['idDocumentoDte', 'codigoTipoDte', 'nombre', 'nombre_corto', 'activo'],

			'attr_types'		=> [
				'idDocumentoDte' => 'INT',
				'codigoTipoDte' => 'INT',
				'nombre' => 'STR',
				'nombre_corto' => 'STR',
				'activo' => 'INT'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idDocumentoDte'],

			'autoincrement' 	=> null,

			'nullable'			=> ['codigoTipoDte', 'nombre', 'nombre_corto', 'activo'],

			'required'			=> ['idDocumentoDte'],

			'uniques'			=> [],

			'rules' 			=> [
				'idDocumentoDte' => ['type' => 'int', 'required' => true],
				'codigoTipoDte' => ['type' => 'int'],
				'nombre' => ['type' => 'str', 'max' => 45],
				'nombre_corto' => ['type' => 'str', 'max' => 45],
				'activo' => ['type' => 'int']
			],

			'fks' 				=> [],

			'relationships' => [
				'venta' => [
					['venta.idDocumentoDte','documentodte.idDocumentoDte']
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
        1 => 'idDocumentoDte',
      ),
      1 => 
      array (
        0 => 'documentodte',
        1 => 'idDocumentoDte',
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

