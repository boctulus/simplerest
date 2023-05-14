<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_INSCRIPCIONESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_INSCRIPCIONES',

			'id_name'		=> 'INS_ID',

			'fields'		=> ['INS_ID', 'INS_TERMINOS_CONDICIONES', 'INS_TRATAMIENTO_DATOS', 'INS_FECHA', 'INS_NOMBRE_ORGANIZACION', 'COMUNAS', 'INS_ENFOQUE', 'INS_CEDULA_REPRESENTANTE', 'INS_CORREO', 'INS_TELEFONO', 'CON_ID', 'created_at', 'updated_at', 'INS_VARIABLES'],

			'attr_types'	=> [
				'INS_ID' => 'INT',
				'INS_TERMINOS_CONDICIONES' => 'INT',
				'INS_TRATAMIENTO_DATOS' => 'INT',
				'INS_FECHA' => 'STR',
				'INS_NOMBRE_ORGANIZACION' => 'STR',
				'COMUNAS' => 'STR',
				'INS_ENFOQUE' => 'STR',
				'INS_CEDULA_REPRESENTANTE' => 'STR',
				'INS_CORREO' => 'STR',
				'INS_TELEFONO' => 'STR',
				'CON_ID' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'INS_VARIABLES' => 'STR'
			],

			'primary'		=> ['INS_ID'],

			'autoincrement' => 'INS_ID',

			'nullable'		=> ['INS_ID', 'INS_TERMINOS_CONDICIONES', 'INS_TRATAMIENTO_DATOS', 'INS_FECHA', 'INS_NOMBRE_ORGANIZACION', 'COMUNAS', 'INS_ENFOQUE', 'INS_CEDULA_REPRESENTANTE', 'INS_CORREO', 'INS_TELEFONO', 'CON_ID', 'created_at', 'updated_at', 'INS_VARIABLES'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'INS_ID' => ['type' => 'int'],
				'INS_TERMINOS_CONDICIONES' => ['type' => 'bool'],
				'INS_TRATAMIENTO_DATOS' => ['type' => 'bool'],
				'INS_FECHA' => ['type' => 'datetime'],
				'INS_NOMBRE_ORGANIZACION' => ['type' => 'str', 'max' => 255],
				'COMUNAS' => ['type' => 'str'],
				'INS_ENFOQUE' => ['type' => 'str', 'max' => 255],
				'INS_CEDULA_REPRESENTANTE' => ['type' => 'str', 'max' => 45],
				'INS_CORREO' => ['type' => 'str', 'max' => 255],
				'INS_TELEFONO' => ['type' => 'str', 'max' => 45],
				'CON_ID' => ['type' => 'int'],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime'],
				'INS_VARIABLES' => ['type' => 'str']
			],

			'fks' 			=> ['CON_ID'],

			'relationships' => [
				'TBL_CONVOCATORIAS' => [
					['TBL_CONVOCATORIAS.CON_ID','TBL_INSCRIPCIONES.CON_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_CONVOCATORIAS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_CONVOCATORIAS',
        1 => 'CON_ID',
      ),
      1 => 
      array (
        0 => 'TBL_INSCRIPCIONES',
        1 => 'CON_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_CONVOCATORIAS' => [
					['TBL_CONVOCATORIAS.CON_ID','TBL_INSCRIPCIONES.CON_ID']
				]
			],

			'expanded_relationships_from' => array (
  'TBL_CONVOCATORIAS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_CONVOCATORIAS',
        1 => 'CON_ID',
      ),
      1 => 
      array (
        0 => 'TBL_INSCRIPCIONES',
        1 => 'CON_ID',
      ),
    ),
  ),
)
		];
	}	
}

