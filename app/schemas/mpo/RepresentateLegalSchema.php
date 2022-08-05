<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class RepresentateLegalSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'representate_legal',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'tipo_doc' => 'STR',
				'nro_doc' => 'STR',
				'departamento_exp' => 'STR',
				'municipio_exp' => 'STR',
				'nombres' => 'STR',
				'apellidos' => 'STR',
				'fecha_nacimiento' => 'STR',
				'genero' => 'STR',
				'profesion_oficio' => 'STR',
				'tarjeta_profesional' => 'STR',
				'estado_civil' => 'STR',
				'estado_laboral' => 'STR',
				'tel_fijo' => 'STR',
				'tel_celular' => 'STR',
				'email' => 'STR',
				'direccion' => 'STR',
				'zona' => 'STR',
				'barrio' => 'STR',
				'sabe_leer' => 'INT',
				'sabe_escribir' => 'INT',
				'nivel_escolaridad_id' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'tarjeta_profesional', 'tel_fijo', 'zona', 'created_at', 'updated_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'tipo_doc' => ['type' => 'str', 'max' => 20, 'required' => true],
				'nro_doc' => ['type' => 'str', 'max' => 25, 'required' => true],
				'departamento_exp' => ['type' => 'str', 'max' => 30, 'required' => true],
				'municipio_exp' => ['type' => 'str', 'max' => 50, 'required' => true],
				'nombres' => ['type' => 'str', 'max' => 80, 'required' => true],
				'apellidos' => ['type' => 'str', 'max' => 100, 'required' => true],
				'fecha_nacimiento' => ['type' => 'datetime', 'required' => true],
				'genero' => ['type' => 'str', 'max' => 15, 'required' => true],
				'profesion_oficio' => ['type' => 'str', 'max' => 40, 'required' => true],
				'tarjeta_profesional' => ['type' => 'str', 'max' => 20],
				'estado_civil' => ['type' => 'str', 'max' => 20, 'required' => true],
				'estado_laboral' => ['type' => 'str', 'max' => 20, 'required' => true],
				'tel_fijo' => ['type' => 'str', 'max' => 20],
				'tel_celular' => ['type' => 'str', 'max' => 20, 'required' => true],
				'email' => ['type' => 'str', 'max' => 255, 'required' => true],
				'direccion' => ['type' => 'str', 'max' => 255, 'required' => true],
				'zona' => ['type' => 'str', 'max' => 255],
				'barrio' => ['type' => 'str', 'max' => 255, 'required' => true],
				'sabe_leer' => ['type' => 'bool', 'required' => true],
				'sabe_escribir' => ['type' => 'bool', 'required' => true],
				'nivel_escolaridad_id' => ['type' => 'int', 'min' => 0, 'required' => true],
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

