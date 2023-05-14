<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_REPRESENTANTES_LEGALESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_REPRESENTANTES_LEGALES',

			'id_name'		=> 'ID_RPL',

			'fields'		=> ['ID_RPL', 'RPL_NOMBRES', 'RPL_APELLIDOS', 'TIPO_DOC_ID', 'RPL_NRO_DOC', 'DEPARTAMENTO_EXP_ID', 'MUNICIPIO_EXP_ID', 'RPL_FECHA_NACIMIENTO', 'GENERO_ID', 'RPL_PROFESION_OFICIO', 'RPL_TARJETA_PROFESIONAL', 'ESTADO_CIVIL_ID', 'ESTADO_LABORAL_ID', 'RPL_TEL_FIJO', 'RPL_TEL_CELULAR', 'RPL_EMAIL', 'RPL_DIRECCION', 'RPL_ZONA', 'RPL_BARRIO', 'RPL_SABE_LEER', 'RPL_SABE_ESCRIBIR', 'NIVEL_ESCOLARIDAD_ID', 'RPL_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_RPL' => 'INT',
				'RPL_NOMBRES' => 'STR',
				'RPL_APELLIDOS' => 'STR',
				'TIPO_DOC_ID' => 'INT',
				'RPL_NRO_DOC' => 'STR',
				'DEPARTAMENTO_EXP_ID' => 'INT',
				'MUNICIPIO_EXP_ID' => 'INT',
				'RPL_FECHA_NACIMIENTO' => 'STR',
				'GENERO_ID' => 'INT',
				'RPL_PROFESION_OFICIO' => 'STR',
				'RPL_TARJETA_PROFESIONAL' => 'INT',
				'ESTADO_CIVIL_ID' => 'INT',
				'ESTADO_LABORAL_ID' => 'INT',
				'RPL_TEL_FIJO' => 'STR',
				'RPL_TEL_CELULAR' => 'STR',
				'RPL_EMAIL' => 'STR',
				'RPL_DIRECCION' => 'STR',
				'RPL_ZONA' => 'STR',
				'RPL_BARRIO' => 'STR',
				'RPL_SABE_LEER' => 'INT',
				'RPL_SABE_ESCRIBIR' => 'INT',
				'NIVEL_ESCOLARIDAD_ID' => 'INT',
				'RPL_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_RPL'],

			'autoincrement' => 'ID_RPL',

			'nullable'		=> ['ID_RPL', 'RPL_TEL_FIJO', 'RPL_ZONA', 'RPL_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['RPL_NOMBRES', 'RPL_APELLIDOS', 'TIPO_DOC_ID', 'RPL_NRO_DOC', 'DEPARTAMENTO_EXP_ID', 'MUNICIPIO_EXP_ID', 'RPL_FECHA_NACIMIENTO', 'GENERO_ID', 'RPL_PROFESION_OFICIO', 'RPL_TARJETA_PROFESIONAL', 'ESTADO_CIVIL_ID', 'ESTADO_LABORAL_ID', 'RPL_TEL_CELULAR', 'RPL_EMAIL', 'RPL_DIRECCION', 'RPL_BARRIO', 'RPL_SABE_LEER', 'RPL_SABE_ESCRIBIR', 'NIVEL_ESCOLARIDAD_ID'],

			'uniques'		=> [],

			'rules' 		=> [
				'ID_RPL' => ['type' => 'int', 'min' => 0],
				'RPL_NOMBRES' => ['type' => 'str', 'max' => 80, 'required' => true],
				'RPL_APELLIDOS' => ['type' => 'str', 'max' => 100, 'required' => true],
				'TIPO_DOC_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'RPL_NRO_DOC' => ['type' => 'str', 'max' => 25, 'required' => true],
				'DEPARTAMENTO_EXP_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'MUNICIPIO_EXP_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'RPL_FECHA_NACIMIENTO' => ['type' => 'date', 'required' => true],
				'GENERO_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'RPL_PROFESION_OFICIO' => ['type' => 'str', 'max' => 40, 'required' => true],
				'RPL_TARJETA_PROFESIONAL' => ['type' => 'bool', 'required' => true],
				'ESTADO_CIVIL_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'ESTADO_LABORAL_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'RPL_TEL_FIJO' => ['type' => 'str', 'max' => 30],
				'RPL_TEL_CELULAR' => ['type' => 'str', 'max' => 30, 'required' => true],
				'RPL_EMAIL' => ['type' => 'str', 'max' => 255, 'required' => true],
				'RPL_DIRECCION' => ['type' => 'str', 'max' => 255, 'required' => true],
				'RPL_ZONA' => ['type' => 'str', 'max' => 255],
				'RPL_BARRIO' => ['type' => 'str', 'max' => 255, 'required' => true],
				'RPL_SABE_LEER' => ['type' => 'bool', 'required' => true],
				'RPL_SABE_ESCRIBIR' => ['type' => 'bool', 'required' => true],
				'NIVEL_ESCOLARIDAD_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'RPL_BORRADO' => ['type' => 'bool'],
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

