<?php

namespace Boctulus\FriendlyposWeb\Schemas;

use Boctulus\Simplerest\Core\Interfaces\ISchema;

### IMPORTS

class DocumentoEstadoSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'		=> 'documento_estado',

			'id_name'			=> 'idDocumento_estado',

			'fields'			=> ['idDocumento_estado', 'estado', 'glosa'],

			'attr_types'		=> [
				'idDocumento_estado' => 'INT',
				'estado' => 'STR',
				'glosa' => 'STR'
			],

			'attr_type_detail'	=> [

			],

			'primary'			=> ['idDocumento_estado'],

			'autoincrement' 	=> 'idDocumento_estado',

			'nullable'			=> ['idDocumento_estado', 'estado', 'glosa'],

			'required'			=> [],

			'uniques'			=> [],

			'rules' 			=> [
				'idDocumento_estado' => ['type' => 'int'],
				'estado' => ['type' => 'str', 'max' => 45],
				'glosa' => ['type' => 'str', 'max' => 100]
			],

			'fks' 				=> [],

			'relationships' => [
				
			],

			'expanded_relationships' => array (
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}

