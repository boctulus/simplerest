<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_ENTIDADES_REG_GRUPOS_POBLACIONALESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_ENTIDADES_REG_GRUPOS_POBLACIONALES',

			'id_name'		=> 'ID_ERP',

			'fields'		=> ['ID_ERP', 'ENTIDAD_REG_ID', 'GR_POBL_ID', 'ERP_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_ERP' => 'INT',
				'ENTIDAD_REG_ID' => 'INT',
				'GR_POBL_ID' => 'INT',
				'ERP_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_ERP'],

			'autoincrement' => 'ID_ERP',

			'nullable'		=> ['ID_ERP', 'ERP_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['ENTIDAD_REG_ID', 'GR_POBL_ID'],

			'uniques'		=> [],

			'rules' 		=> [
				'ID_ERP' => ['type' => 'int', 'min' => 0],
				'ENTIDAD_REG_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'GR_POBL_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'ERP_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

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

