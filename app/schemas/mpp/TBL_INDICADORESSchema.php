<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_INDICADORESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_INDICADORES',

			'id_name'		=> 'IND_ID',

			'fields'		=> ['IND_ID', 'IND_NOMBRE', 'IND_OBJETIVO', 'IND_ALCANCE', 'IND_PROCESO', 'IND_PRODUCTO', 'IND_SERVICIO', 'IND_FORMULA', 'IND_UNIDAD_MEDIDA', 'IND_TIPO', 'IND_META', 'IND_LINEA_BASE', 'IND_PERIODICIDAD', 'IND_FUENTE_INFORMACION', 'IND_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'IND_ID' => 'INT',
				'IND_NOMBRE' => 'STR',
				'IND_OBJETIVO' => 'STR',
				'IND_ALCANCE' => 'STR',
				'IND_PROCESO' => 'STR',
				'IND_PRODUCTO' => 'STR',
				'IND_SERVICIO' => 'STR',
				'IND_FORMULA' => 'STR',
				'IND_UNIDAD_MEDIDA' => 'STR',
				'IND_TIPO' => 'STR',
				'IND_META' => 'STR',
				'IND_LINEA_BASE' => 'STR',
				'IND_PERIODICIDAD' => 'STR',
				'IND_FUENTE_INFORMACION' => 'STR',
				'IND_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['IND_ID'],

			'autoincrement' => 'IND_ID',

			'nullable'		=> ['IND_ID', 'IND_NOMBRE', 'IND_OBJETIVO', 'IND_ALCANCE', 'IND_PROCESO', 'IND_PRODUCTO', 'IND_SERVICIO', 'IND_FORMULA', 'IND_UNIDAD_MEDIDA', 'IND_TIPO', 'IND_META', 'IND_LINEA_BASE', 'IND_PERIODICIDAD', 'IND_FUENTE_INFORMACION', 'IND_BORRADO', 'created_at', 'updated_at'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'IND_ID' => ['type' => 'int'],
				'IND_NOMBRE' => ['type' => 'str', 'max' => 255],
				'IND_OBJETIVO' => ['type' => 'str', 'max' => 100],
				'IND_ALCANCE' => ['type' => 'str', 'max' => 45],
				'IND_PROCESO' => ['type' => 'str', 'max' => 100],
				'IND_PRODUCTO' => ['type' => 'str', 'max' => 100],
				'IND_SERVICIO' => ['type' => 'str', 'max' => 100],
				'IND_FORMULA' => ['type' => 'str', 'max' => 100],
				'IND_UNIDAD_MEDIDA' => ['type' => 'str', 'max' => 45],
				'IND_TIPO' => ['type' => 'str', 'max' => 45],
				'IND_META' => ['type' => 'str', 'max' => 100],
				'IND_LINEA_BASE' => ['type' => 'str', 'max' => 100],
				'IND_PERIODICIDAD' => ['type' => 'str', 'max' => 100],
				'IND_FUENTE_INFORMACION' => ['type' => 'str', 'max' => 100],
				'IND_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime']
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

