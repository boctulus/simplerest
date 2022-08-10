<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class RepresentantesLegalesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'representantes_legales',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'tipo_doc_id', 'nro_doc', 'departamento_exp_id', 'municipio_exp_id', 'nombres', 'apellidos', 'fecha_nacimiento', 'genero_id', 'profesion_oficio', 'tarjeta_profesional', 'estado_civil_id', 'estado_laboral_id', 'tel_fijo', 'tel_celular', 'email', 'direccion', 'zona', 'barrio', 'sabe_leer', 'sabe_escribir', 'nivel_escolaridad_id', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'tipo_doc_id' => 'INT',
				'nro_doc' => 'STR',
				'departamento_exp_id' => 'INT',
				'municipio_exp_id' => 'INT',
				'nombres' => 'STR',
				'apellidos' => 'STR',
				'fecha_nacimiento' => 'STR',
				'genero_id' => 'INT',
				'profesion_oficio' => 'STR',
				'tarjeta_profesional' => 'INT',
				'estado_civil_id' => 'INT',
				'estado_laboral_id' => 'INT',
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

			'nullable'		=> ['id', 'tel_fijo', 'zona', 'created_at', 'updated_at'],

			'required'		=> ['tipo_doc_id', 'nro_doc', 'departamento_exp_id', 'municipio_exp_id', 'nombres', 'apellidos', 'fecha_nacimiento', 'genero_id', 'profesion_oficio', 'tarjeta_profesional', 'estado_civil_id', 'estado_laboral_id', 'tel_celular', 'email', 'direccion', 'barrio', 'sabe_leer', 'sabe_escribir', 'nivel_escolaridad_id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'tipo_doc_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'nro_doc' => ['type' => 'str', 'max' => 25, 'required' => true],
				'departamento_exp_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'municipio_exp_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'nombres' => ['type' => 'str', 'max' => 80, 'required' => true],
				'apellidos' => ['type' => 'str', 'max' => 100, 'required' => true],
				'fecha_nacimiento' => ['type' => 'date', 'required' => true],
				'genero_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'profesion_oficio' => ['type' => 'str', 'max' => 40, 'required' => true],
				'tarjeta_profesional' => ['type' => 'bool', 'required' => true],
				'estado_civil_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'estado_laboral_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'tel_fijo' => ['type' => 'str', 'max' => 30],
				'tel_celular' => ['type' => 'str', 'max' => 30, 'required' => true],
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

			'fks' 			=> ['departamento_exp_id', 'estado_civil_id', 'estado_laboral_id', 'genero_id', 'municipio_exp_id', 'nivel_escolaridad_id', 'tipo_doc_id'],

			'relationships' => [
				'departamentos' => [
					['departamentos.id','representantes_legales.departamento_exp_id']
				],
				'estados_civiles' => [
					['estados_civiles.id','representantes_legales.estado_civil_id']
				],
				'estados_laborales' => [
					['estados_laborales.id','representantes_legales.estado_laboral_id']
				],
				'generos' => [
					['generos.id','representantes_legales.genero_id']
				],
				'municipios' => [
					['municipios.id','representantes_legales.municipio_exp_id']
				],
				'niveles_escolaridad' => [
					['niveles_escolaridad.id','representantes_legales.nivel_escolaridad_id']
				],
				'tipos_doc' => [
					['tipos_doc.id','representantes_legales.tipo_doc_id']
				],
				'org_comunales' => [
					['org_comunales.representante_legal_id','representantes_legales.id']
				]
			],

			'expanded_relationships' => array (
  'departamentos' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'departamentos',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'departamento_exp_id',
      ),
    ),
  ),
  'estados_civiles' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'estados_civiles',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'estado_civil_id',
      ),
    ),
  ),
  'estados_laborales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'estados_laborales',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'estado_laboral_id',
      ),
    ),
  ),
  'generos' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'generos',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'genero_id',
      ),
    ),
  ),
  'municipios' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'municipios',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'municipio_exp_id',
      ),
    ),
  ),
  'niveles_escolaridad' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'niveles_escolaridad',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'nivel_escolaridad_id',
      ),
    ),
  ),
  'tipos_doc' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'tipos_doc',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'tipo_doc_id',
      ),
    ),
  ),
  'org_comunales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'org_comunales',
        1 => 'representante_legal_id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'departamentos' => [
					['departamentos.id','representantes_legales.departamento_exp_id']
				],
				'estados_civiles' => [
					['estados_civiles.id','representantes_legales.estado_civil_id']
				],
				'estados_laborales' => [
					['estados_laborales.id','representantes_legales.estado_laboral_id']
				],
				'generos' => [
					['generos.id','representantes_legales.genero_id']
				],
				'municipios' => [
					['municipios.id','representantes_legales.municipio_exp_id']
				],
				'niveles_escolaridad' => [
					['niveles_escolaridad.id','representantes_legales.nivel_escolaridad_id']
				],
				'tipos_doc' => [
					['tipos_doc.id','representantes_legales.tipo_doc_id']
				]
			],

			'expanded_relationships_from' => array (
  'departamentos' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'departamentos',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'departamento_exp_id',
      ),
    ),
  ),
  'estados_civiles' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'estados_civiles',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'estado_civil_id',
      ),
    ),
  ),
  'estados_laborales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'estados_laborales',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'estado_laboral_id',
      ),
    ),
  ),
  'generos' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'generos',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'genero_id',
      ),
    ),
  ),
  'municipios' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'municipios',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'municipio_exp_id',
      ),
    ),
  ),
  'niveles_escolaridad' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'niveles_escolaridad',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'nivel_escolaridad_id',
      ),
    ),
  ),
  'tipos_doc' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'tipos_doc',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'representantes_legales',
        1 => 'tipo_doc_id',
      ),
    ),
  ),
)
		];
	}	
}

