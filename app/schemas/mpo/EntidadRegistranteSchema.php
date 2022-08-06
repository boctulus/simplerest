<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class EntidadRegistranteSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'entidad_registrante',

			'id_name'		=> 'id',

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
				'instrumento_planeacion_id' => 'INT',
				'tiempo_formulacion' => 'STR',
				'porc_cumpl_plan_ult_anno' => 'INT',
				'plan_de_trabajo' => 'STR',
				'plan_de_trabajo_pla_pub' => 'STR',
				'plan_de_trabajo_pol_pub' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'portafolio_servicios', 'presencia_geografica', 'created_at', 'updated_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'num_radicado' => ['type' => 'int', 'required' => true],
				'fecha_registro' => ['type' => 'datetime', 'required' => true],
				'_cert_vigente_gob' => ['type' => 'bool', 'required' => true],
				'sede_organizacion' => ['type' => 'str', 'max' => 255, 'required' => true],
				'actividades' => ['type' => 'str', 'required' => true],
				'portafolio_servicios' => ['type' => 'str'],
				'presencia_geografica' => ['type' => 'str'],
				'grupo_poblacional_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'mision' => ['type' => 'str', 'required' => true],
				'vision' => ['type' => 'str', 'required' => true],
				'objetivos' => ['type' => 'str', 'required' => true],
				'cant_miembros' => ['type' => 'int', 'min' => 0, 'required' => true],
				'organo_direccion' => ['type' => 'str', 'max' => 255, 'required' => true],
				'cant_miembros_organo_dir' => ['type' => 'int', 'min' => 0, 'required' => true],
				'_completo_organo_dir' => ['type' => 'bool', 'required' => true],
				'como_elige_organo_dir' => ['type' => 'str', 'max' => 255, 'required' => true],
				'denominacion_organo_dir' => ['type' => 'str', 'max' => 255, 'required' => true],
				'frec_reunion_organo_dir' => ['type' => 'str', 'max' => 255, 'required' => true],
				'frec_reunion_miembros' => ['type' => 'str', 'max' => 255, 'required' => true],
				'instrumento_planeacion_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'tiempo_formulacion' => ['type' => 'str', 'max' => 255, 'required' => true],
				'porc_cumpl_plan_ult_anno' => ['type' => 'bool', 'min' => 0, 'required' => true],
				'plan_de_trabajo' => ['type' => 'str', 'required' => true],
				'plan_de_trabajo_pla_pub' => ['type' => 'str', 'required' => true],
				'plan_de_trabajo_pol_pub' => ['type' => 'str', 'required' => true],
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

