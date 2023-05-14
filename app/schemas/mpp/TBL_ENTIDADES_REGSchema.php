<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_ENTIDADES_REGSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_ENTIDADES_REG',

			'id_name'		=> 'ID_ERG',

			'fields'		=> ['ID_ERG', 'ERG_NUM_RADICADO', 'ERG_FECHA_REGISTRO', 'ERG_CERT_VIGENTE_GOB', 'ERG_SEDE_ORGANIZACION', 'ERG_ACTIVIDADES', 'ERG_PORTAFOLIO_SERVICIOS', 'ERG_PRESENCIA_GEOGRAFICA', 'ERG_GRUPO_POBLACIONAL_ID', 'ERG_MISION', 'ERG_VISION', 'ERG_OBJETIVOS', 'ERG_CANT_MIEMBROS', 'ERG_ORGANO_DIRECCION', 'ERG_CANT_MIEMBROS_ORGANO_DIR', 'ERG_COMPLETO_ORGANO_DIR', 'ERG_COMO_ELIGE_ORGANO_DIR', 'ERG_DENOMINACION_ORGANO_DIR', 'ERG_FREC_REUNION_ORGANO_DIR', 'ERG_FREC_REUNION_MIEMBROS', 'ERG_TIEMPO_FORMULACION', 'ERG_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_ERG' => 'INT',
				'ERG_NUM_RADICADO' => 'INT',
				'ERG_FECHA_REGISTRO' => 'STR',
				'ERG_CERT_VIGENTE_GOB' => 'INT',
				'ERG_SEDE_ORGANIZACION' => 'STR',
				'ERG_ACTIVIDADES' => 'STR',
				'ERG_PORTAFOLIO_SERVICIOS' => 'STR',
				'ERG_PRESENCIA_GEOGRAFICA' => 'STR',
				'ERG_GRUPO_POBLACIONAL_ID' => 'INT',
				'ERG_MISION' => 'STR',
				'ERG_VISION' => 'STR',
				'ERG_OBJETIVOS' => 'STR',
				'ERG_CANT_MIEMBROS' => 'INT',
				'ERG_ORGANO_DIRECCION' => 'STR',
				'ERG_CANT_MIEMBROS_ORGANO_DIR' => 'INT',
				'ERG_COMPLETO_ORGANO_DIR' => 'INT',
				'ERG_COMO_ELIGE_ORGANO_DIR' => 'STR',
				'ERG_DENOMINACION_ORGANO_DIR' => 'STR',
				'ERG_FREC_REUNION_ORGANO_DIR' => 'STR',
				'ERG_FREC_REUNION_MIEMBROS' => 'STR',
				'ERG_TIEMPO_FORMULACION' => 'STR',
				'ERG_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_ERG'],

			'autoincrement' => 'ID_ERG',

			'nullable'		=> ['ID_ERG', 'ERG_PORTAFOLIO_SERVICIOS', 'ERG_PRESENCIA_GEOGRAFICA', 'ERG_MISION', 'ERG_VISION', 'ERG_OBJETIVOS', 'ERG_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['ERG_NUM_RADICADO', 'ERG_FECHA_REGISTRO', 'ERG_CERT_VIGENTE_GOB', 'ERG_SEDE_ORGANIZACION', 'ERG_ACTIVIDADES', 'ERG_GRUPO_POBLACIONAL_ID', 'ERG_CANT_MIEMBROS', 'ERG_ORGANO_DIRECCION', 'ERG_CANT_MIEMBROS_ORGANO_DIR', 'ERG_COMPLETO_ORGANO_DIR', 'ERG_COMO_ELIGE_ORGANO_DIR', 'ERG_DENOMINACION_ORGANO_DIR', 'ERG_FREC_REUNION_ORGANO_DIR', 'ERG_FREC_REUNION_MIEMBROS', 'ERG_TIEMPO_FORMULACION'],

			'uniques'		=> [],

			'rules' 		=> [
				'ID_ERG' => ['type' => 'int', 'min' => 0],
				'ERG_NUM_RADICADO' => ['type' => 'int', 'required' => true],
				'ERG_FECHA_REGISTRO' => ['type' => 'date', 'required' => true],
				'ERG_CERT_VIGENTE_GOB' => ['type' => 'bool', 'required' => true],
				'ERG_SEDE_ORGANIZACION' => ['type' => 'str', 'max' => 255, 'required' => true],
				'ERG_ACTIVIDADES' => ['type' => 'str', 'required' => true],
				'ERG_PORTAFOLIO_SERVICIOS' => ['type' => 'str'],
				'ERG_PRESENCIA_GEOGRAFICA' => ['type' => 'str'],
				'ERG_GRUPO_POBLACIONAL_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'ERG_MISION' => ['type' => 'str'],
				'ERG_VISION' => ['type' => 'str'],
				'ERG_OBJETIVOS' => ['type' => 'str'],
				'ERG_CANT_MIEMBROS' => ['type' => 'int', 'min' => 0, 'required' => true],
				'ERG_ORGANO_DIRECCION' => ['type' => 'str', 'max' => 255, 'required' => true],
				'ERG_CANT_MIEMBROS_ORGANO_DIR' => ['type' => 'int', 'min' => 0, 'required' => true],
				'ERG_COMPLETO_ORGANO_DIR' => ['type' => 'bool', 'required' => true],
				'ERG_COMO_ELIGE_ORGANO_DIR' => ['type' => 'str', 'max' => 255, 'required' => true],
				'ERG_DENOMINACION_ORGANO_DIR' => ['type' => 'str', 'max' => 255, 'required' => true],
				'ERG_FREC_REUNION_ORGANO_DIR' => ['type' => 'str', 'max' => 255, 'required' => true],
				'ERG_FREC_REUNION_MIEMBROS' => ['type' => 'str', 'max' => 255, 'required' => true],
				'ERG_TIEMPO_FORMULACION' => ['type' => 'str', 'max' => 255, 'required' => true],
				'ERG_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> [],

			'relationships' => [
				'TBL_ORG_COMUNAL_ENTIDAD_REG' => [
					['TBL_ORG_COMUNAL_ENTIDAD_REG.ENTIDAD_REG_ID','TBL_ENTIDADES_REG.ID_ERG']
				]
			],

			'expanded_relationships' => array (
  'TBL_ORG_COMUNAL_ENTIDAD_REG' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ORG_COMUNAL_ENTIDAD_REG',
        1 => 'ENTIDAD_REG_ID',
      ),
      1 => 
      array (
        0 => 'TBL_ENTIDADES_REG',
        1 => 'ID_ERG',
      ),
    ),
  ),
),

			'relationships_from' => [
				
			],

			'expanded_relationships_from' => array (
)
		];
	}	
}

