<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class OrgComunalSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'org_comunal',

			'id_name'		=> 'id',

			'attr_types'	=> [
				'id' => 'INT',
				'nombre_org_comunal' => 'STR',
				'fecha_aplicacion' => 'STR',
				'zona' => 'STR',
				'comuna_corregimiento' => 'STR',
				'direccion' => 'STR',
				'representante' => 'STR',
				'tipo_organismo_org_comunal_id' => 'INT',
				'estado_seguimiento_id' => 'INT',
				'institucion_avalante' => 'STR',
				'sector_actividad_org_comunal_id' => 'INT',
				'objetivo_social' => 'STR',
				'certificaciones_que_emite_org_comunal_id' => 'INT',
				'areas_tematicas' => 'STR',
				'fortalezas_organizacion' => 'STR',
				'redes' => 'STR',
				'_tiene_estatutos' => 'INT',
				'_registrada_camara_com' => 'INT',
				'num_radicado_camara_com' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'zona', 'representante', 'institucion_avalante', 'redes', 'num_radicado_camara_com', 'created_at', 'updated_at'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'nombre_org_comunal' => ['type' => 'str', 'max' => 60, 'required' => true],
				'fecha_aplicacion' => ['type' => 'datetime', 'required' => true],
				'zona' => ['type' => 'str', 'max' => 30],
				'comuna_corregimiento' => ['type' => 'str', 'max' => 50, 'required' => true],
				'direccion' => ['type' => 'str', 'max' => 255, 'required' => true],
				'representante' => ['type' => 'str', 'max' => 255],
				'tipo_organismo_org_comunal_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'estado_seguimiento_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'institucion_avalante' => ['type' => 'str'],
				'sector_actividad_org_comunal_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'objetivo_social' => ['type' => 'str', 'max' => 255, 'required' => true],
				'certificaciones_que_emite_org_comunal_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'areas_tematicas' => ['type' => 'str', 'required' => true],
				'fortalezas_organizacion' => ['type' => 'str', 'required' => true],
				'redes' => ['type' => 'str'],
				'_tiene_estatutos' => ['type' => 'bool', 'required' => true],
				'_registrada_camara_com' => ['type' => 'bool', 'required' => true],
				'num_radicado_camara_com' => ['type' => 'int'],
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

