<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class EntidadesRegistrantesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'entidades_registrantes',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'num_radicado', 'fecha_registro', '_cert_vigente_gob', 'sede_organizacion', 'actividades', 'portafolio_servicios', 'presencia_geografica', 'grupo_poblacional_id', 'mision', 'vision', 'objetivos', 'cant_miembros', 'organo_direccion', 'cant_miembros_organo_dir', '_completo_organo_dir', 'como_elige_organo_dir', 'denominacion_organo_dir', 'frec_reunion_organo_dir', 'frec_reunion_miembros', 'tiempo_formulacion', 'deleted_at', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'num_radicado' => 'INT',
				'fecha_registro' => 'STR',
				'_cert_vigente_gob' => 'INT',
				'sede_organizacion' => 'STR',
				'actividades' => 'STR',
				'portafolio_servicios' => 'STR',
				'presencia_geografica' => 'STR',
				'grupo_poblacional_id' => 'INT',
				'mision' => 'STR',
				'vision' => 'STR',
				'objetivos' => 'STR',
				'cant_miembros' => 'INT',
				'organo_direccion' => 'STR',
				'cant_miembros_organo_dir' => 'INT',
				'_completo_organo_dir' => 'INT',
				'como_elige_organo_dir' => 'STR',
				'denominacion_organo_dir' => 'STR',
				'frec_reunion_organo_dir' => 'STR',
				'frec_reunion_miembros' => 'STR',
				'tiempo_formulacion' => 'STR',
				'deleted_at' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'portafolio_servicios', 'presencia_geografica', 'mision', 'vision', 'objetivos', 'deleted_at', 'created_at', 'updated_at'],

			'required'		=> ['num_radicado', 'fecha_registro', '_cert_vigente_gob', 'sede_organizacion', 'actividades', 'grupo_poblacional_id', 'cant_miembros', 'organo_direccion', 'cant_miembros_organo_dir', '_completo_organo_dir', 'como_elige_organo_dir', 'denominacion_organo_dir', 'frec_reunion_organo_dir', 'frec_reunion_miembros', 'tiempo_formulacion'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'num_radicado' => ['type' => 'int', 'required' => true],
				'fecha_registro' => ['type' => 'date', 'required' => true],
				'_cert_vigente_gob' => ['type' => 'bool', 'required' => true],
				'sede_organizacion' => ['type' => 'str', 'max' => 255, 'required' => true],
				'actividades' => ['type' => 'str', 'required' => true],
				'portafolio_servicios' => ['type' => 'str'],
				'presencia_geografica' => ['type' => 'str'],
				'grupo_poblacional_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'mision' => ['type' => 'str'],
				'vision' => ['type' => 'str'],
				'objetivos' => ['type' => 'str'],
				'cant_miembros' => ['type' => 'int', 'min' => 0, 'required' => true],
				'organo_direccion' => ['type' => 'str', 'max' => 255, 'required' => true],
				'cant_miembros_organo_dir' => ['type' => 'int', 'min' => 0, 'required' => true],
				'_completo_organo_dir' => ['type' => 'bool', 'required' => true],
				'como_elige_organo_dir' => ['type' => 'str', 'max' => 255, 'required' => true],
				'denominacion_organo_dir' => ['type' => 'str', 'max' => 255, 'required' => true],
				'frec_reunion_organo_dir' => ['type' => 'str', 'max' => 255, 'required' => true],
				'frec_reunion_miembros' => ['type' => 'str', 'max' => 255, 'required' => true],
				'tiempo_formulacion' => ['type' => 'str', 'max' => 255, 'required' => true],
				'deleted_at' => ['type' => 'timestamp'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> ['grupo_poblacional_id'],

			'relationships' => [
				'grupos_poblacionales' => [
					['grupos_poblacionales.id','entidades_registrantes.grupo_poblacional_id']
				],
				'org_comunal_entidad_registrante' => [
					['org_comunal_entidad_registrante.entidad_registrante_id','entidades_registrantes.id']
				]
			],

			'expanded_relationships' => array (
  'grupos_poblacionales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'grupos_poblacionales',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'entidades_registrantes',
        1 => 'grupo_poblacional_id',
      ),
    ),
  ),
  'org_comunal_entidad_registrante' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'org_comunal_entidad_registrante',
        1 => 'entidad_registrante_id',
      ),
      1 => 
      array (
        0 => 'entidades_registrantes',
        1 => 'id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'grupos_poblacionales' => [
					['grupos_poblacionales.id','entidades_registrantes.grupo_poblacional_id']
				]
			],

			'expanded_relationships_from' => array (
  'grupos_poblacionales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'grupos_poblacionales',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'entidades_registrantes',
        1 => 'grupo_poblacional_id',
      ),
    ),
  ),
)
		];
	}	
}

