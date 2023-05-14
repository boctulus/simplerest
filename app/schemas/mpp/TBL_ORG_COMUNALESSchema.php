<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_ORG_COMUNALESSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_ORG_COMUNALES',

			'id_name'		=> 'ID_OCM',

			'fields'		=> ['ID_OCM', 'OCM_NOMBRE', 'OCM_ZONA', 'COMUNA_ID', 'DEPARTAMENTO_ID', 'SUBREGION_ID', 'MUNICIPIO_ID', 'OCM_DIRECCION', 'OCM_DIRECCION_COMPLEMENTO', 'BARRIO_ID', 'NIVEL_ID', 'TIPO_ORGANISMO_ORG_COMUNAL_ID', 'OCM_NIT', 'OCM_CORREO_E', 'OCM_TELEFONO', 'OCM_FECHA_CREACION', 'OCM_FECHA_CONSTITUCION', 'OCM_FECHA_APLICACION', 'OCM_NRO_PERSON_JURID', 'OCM_FECHA_PERSON_JURID', 'ESTADO_PERSON_JURID', 'OCM_FECHA_RESOL', 'OCM_NRO_RESOL', 'GRUPO_INTERES', 'ESTADO_SEGUIMIENTO_ID', 'OCM_INSTITUCION_AVALANTE', 'SECTOR_ACTIVIDAD_ORG_COMUNAL_ID', 'OCM_OBJETIVO_SOCIAL', 'OCM_EMITE_CERTIFICACIONES', 'OCM_AREAS_TEMATICAS', 'OCM_FORTALEZAS_ORGANIZACION', 'OCM_REDES', 'OCM_TIENE_ESTATUTOS', 'OCM_REGISTRADA_CAMARA_COM', 'OCM_NUM_RADICADO_CAMARA_COM', 'OCM_PORC_CUMPL_PLAN_ULT_ANNO', 'OCM_PLAN_DE_TRABAJO', 'OCM_PLAN_DE_TRABAJO_PLA_PUB', 'OCM_PLAN_DE_TRABAJO_POL_PUB', 'INSTRUMENTO_PLANEACION_ID', 'OCM_RECURSOS_ECONOMICOS', 'OCM_FUENTES_FINANCIACION', 'OCM_PRESPUESTO_ANUAL', 'OCM_RECURSOS_PROPIOS', 'PR_EJ_REC_PROP_ID', 'PR_EJ_REC_PUB_ID', 'PR_EJ_COOP_ID', 'OCM_REGISTRO_INGRESOS_Y_GASTOS', 'OCM_OBLIGACIONES_TRIBUTARIAS', 'OCM_CONTADOR', 'OCM_ACTIVIDADES_CONJUNTAS_PRIVADAS', 'OCM_ACTIVIDADES_CONJUNTAS_PUBLICAS', 'OCM_ACTIVIDADES_CONJUNTAS_OTRAS_ORG', 'ESCALA_TERRITORIAL_ID', 'COB_TERRITORIAL', 'OCM_MEDIOS_PROMOCION', 'OCM_LINK_CANAL_EMISORA', 'OCM_RENDICION_CUENTAS', 'OCM_ULTIMA_REND_CUENTA', 'OCM_ACCIONES_CONTROL_SOCIAL', 'OCM_NECESIDADES_ASESORIA', 'OCM_PUNTUACION', 'REPRESENTANTE_LEGAL_ID', 'OCM_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_OCM' => 'INT',
				'OCM_NOMBRE' => 'STR',
				'OCM_ZONA' => 'STR',
				'COMUNA_ID' => 'INT',
				'DEPARTAMENTO_ID' => 'INT',
				'SUBREGION_ID' => 'INT',
				'MUNICIPIO_ID' => 'INT',
				'OCM_DIRECCION' => 'STR',
				'OCM_DIRECCION_COMPLEMENTO' => 'STR',
				'BARRIO_ID' => 'INT',
				'NIVEL_ID' => 'INT',
				'TIPO_ORGANISMO_ORG_COMUNAL_ID' => 'INT',
				'OCM_NIT' => 'STR',
				'OCM_CORREO_E' => 'STR',
				'OCM_TELEFONO' => 'STR',
				'OCM_FECHA_CREACION' => 'STR',
				'OCM_FECHA_CONSTITUCION' => 'STR',
				'OCM_FECHA_APLICACION' => 'STR',
				'OCM_NRO_PERSON_JURID' => 'STR',
				'OCM_FECHA_PERSON_JURID' => 'STR',
				'ESTADO_PERSON_JURID' => 'INT',
				'OCM_FECHA_RESOL' => 'STR',
				'OCM_NRO_RESOL' => 'STR',
				'GRUPO_INTERES' => 'INT',
				'ESTADO_SEGUIMIENTO_ID' => 'INT',
				'OCM_INSTITUCION_AVALANTE' => 'STR',
				'SECTOR_ACTIVIDAD_ORG_COMUNAL_ID' => 'INT',
				'OCM_OBJETIVO_SOCIAL' => 'STR',
				'OCM_EMITE_CERTIFICACIONES' => 'INT',
				'OCM_AREAS_TEMATICAS' => 'STR',
				'OCM_FORTALEZAS_ORGANIZACION' => 'STR',
				'OCM_REDES' => 'STR',
				'OCM_TIENE_ESTATUTOS' => 'INT',
				'OCM_REGISTRADA_CAMARA_COM' => 'INT',
				'OCM_NUM_RADICADO_CAMARA_COM' => 'INT',
				'OCM_PORC_CUMPL_PLAN_ULT_ANNO' => 'INT',
				'OCM_PLAN_DE_TRABAJO' => 'STR',
				'OCM_PLAN_DE_TRABAJO_PLA_PUB' => 'STR',
				'OCM_PLAN_DE_TRABAJO_POL_PUB' => 'STR',
				'INSTRUMENTO_PLANEACION_ID' => 'INT',
				'OCM_RECURSOS_ECONOMICOS' => 'STR',
				'OCM_FUENTES_FINANCIACION' => 'STR',
				'OCM_PRESPUESTO_ANUAL' => 'STR',
				'OCM_RECURSOS_PROPIOS' => 'INT',
				'PR_EJ_REC_PROP_ID' => 'INT',
				'PR_EJ_REC_PUB_ID' => 'INT',
				'PR_EJ_COOP_ID' => 'INT',
				'OCM_REGISTRO_INGRESOS_Y_GASTOS' => 'INT',
				'OCM_OBLIGACIONES_TRIBUTARIAS' => 'INT',
				'OCM_CONTADOR' => 'INT',
				'OCM_ACTIVIDADES_CONJUNTAS_PRIVADAS' => 'STR',
				'OCM_ACTIVIDADES_CONJUNTAS_PUBLICAS' => 'STR',
				'OCM_ACTIVIDADES_CONJUNTAS_OTRAS_ORG' => 'STR',
				'ESCALA_TERRITORIAL_ID' => 'INT',
				'COB_TERRITORIAL' => 'STR',
				'OCM_MEDIOS_PROMOCION' => 'STR',
				'OCM_LINK_CANAL_EMISORA' => 'STR',
				'OCM_RENDICION_CUENTAS' => 'INT',
				'OCM_ULTIMA_REND_CUENTA' => 'STR',
				'OCM_ACCIONES_CONTROL_SOCIAL' => 'STR',
				'OCM_NECESIDADES_ASESORIA' => 'STR',
				'OCM_PUNTUACION' => 'STR',
				'REPRESENTANTE_LEGAL_ID' => 'INT',
				'OCM_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_OCM'],

			'autoincrement' => 'ID_OCM',

			'nullable'		=> ['ID_OCM', 'OCM_ZONA', 'COMUNA_ID', 'OCM_INSTITUCION_AVALANTE', 'OCM_REDES', 'OCM_NUM_RADICADO_CAMARA_COM', 'OCM_RECURSOS_ECONOMICOS', 'OCM_FUENTES_FINANCIACION', 'OCM_ACTIVIDADES_CONJUNTAS_PRIVADAS', 'OCM_ACTIVIDADES_CONJUNTAS_PUBLICAS', 'OCM_ACTIVIDADES_CONJUNTAS_OTRAS_ORG', 'OCM_LINK_CANAL_EMISORA', 'OCM_ACCIONES_CONTROL_SOCIAL', 'OCM_NECESIDADES_ASESORIA', 'OCM_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['OCM_NOMBRE', 'DEPARTAMENTO_ID', 'SUBREGION_ID', 'MUNICIPIO_ID', 'OCM_DIRECCION', 'OCM_DIRECCION_COMPLEMENTO', 'BARRIO_ID', 'NIVEL_ID', 'TIPO_ORGANISMO_ORG_COMUNAL_ID', 'OCM_NIT', 'OCM_CORREO_E', 'OCM_TELEFONO', 'OCM_FECHA_CREACION', 'OCM_FECHA_CONSTITUCION', 'OCM_FECHA_APLICACION', 'OCM_NRO_PERSON_JURID', 'OCM_FECHA_PERSON_JURID', 'ESTADO_PERSON_JURID', 'OCM_FECHA_RESOL', 'OCM_NRO_RESOL', 'GRUPO_INTERES', 'ESTADO_SEGUIMIENTO_ID', 'SECTOR_ACTIVIDAD_ORG_COMUNAL_ID', 'OCM_OBJETIVO_SOCIAL', 'OCM_EMITE_CERTIFICACIONES', 'OCM_AREAS_TEMATICAS', 'OCM_FORTALEZAS_ORGANIZACION', 'OCM_TIENE_ESTATUTOS', 'OCM_REGISTRADA_CAMARA_COM', 'OCM_PORC_CUMPL_PLAN_ULT_ANNO', 'OCM_PLAN_DE_TRABAJO', 'OCM_PLAN_DE_TRABAJO_PLA_PUB', 'OCM_PLAN_DE_TRABAJO_POL_PUB', 'INSTRUMENTO_PLANEACION_ID', 'OCM_PRESPUESTO_ANUAL', 'OCM_RECURSOS_PROPIOS', 'PR_EJ_REC_PROP_ID', 'PR_EJ_REC_PUB_ID', 'PR_EJ_COOP_ID', 'OCM_REGISTRO_INGRESOS_Y_GASTOS', 'OCM_OBLIGACIONES_TRIBUTARIAS', 'OCM_CONTADOR', 'ESCALA_TERRITORIAL_ID', 'COB_TERRITORIAL', 'OCM_MEDIOS_PROMOCION', 'OCM_RENDICION_CUENTAS', 'OCM_ULTIMA_REND_CUENTA', 'OCM_PUNTUACION', 'REPRESENTANTE_LEGAL_ID'],

			'uniques'		=> ['OCM_NIT'],

			'rules' 		=> [
				'ID_OCM' => ['type' => 'int', 'min' => 0],
				'OCM_NOMBRE' => ['type' => 'str', 'max' => 60, 'required' => true],
				'OCM_ZONA' => ['type' => 'str', 'max' => 30],
				'COMUNA_ID' => ['type' => 'int'],
				'DEPARTAMENTO_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'SUBREGION_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'MUNICIPIO_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'OCM_DIRECCION' => ['type' => 'str', 'max' => 30, 'required' => true],
				'OCM_DIRECCION_COMPLEMENTO' => ['type' => 'str', 'max' => 50, 'required' => true],
				'BARRIO_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'NIVEL_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'TIPO_ORGANISMO_ORG_COMUNAL_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'OCM_NIT' => ['type' => 'str', 'max' => 15, 'required' => true],
				'OCM_CORREO_E' => ['type' => 'str', 'max' => 255, 'required' => true],
				'OCM_TELEFONO' => ['type' => 'str', 'max' => 30, 'required' => true],
				'OCM_FECHA_CREACION' => ['type' => 'date', 'required' => true],
				'OCM_FECHA_CONSTITUCION' => ['type' => 'date', 'required' => true],
				'OCM_FECHA_APLICACION' => ['type' => 'date', 'required' => true],
				'OCM_NRO_PERSON_JURID' => ['type' => 'str', 'max' => 50, 'required' => true],
				'OCM_FECHA_PERSON_JURID' => ['type' => 'date', 'required' => true],
				'ESTADO_PERSON_JURID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'OCM_FECHA_RESOL' => ['type' => 'date', 'required' => true],
				'OCM_NRO_RESOL' => ['type' => 'str', 'max' => 50, 'required' => true],
				'GRUPO_INTERES' => ['type' => 'int', 'min' => 0, 'required' => true],
				'ESTADO_SEGUIMIENTO_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'OCM_INSTITUCION_AVALANTE' => ['type' => 'str'],
				'SECTOR_ACTIVIDAD_ORG_COMUNAL_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'OCM_OBJETIVO_SOCIAL' => ['type' => 'str', 'max' => 255, 'required' => true],
				'OCM_EMITE_CERTIFICACIONES' => ['type' => 'bool', 'min' => 0, 'required' => true],
				'OCM_AREAS_TEMATICAS' => ['type' => 'str', 'required' => true],
				'OCM_FORTALEZAS_ORGANIZACION' => ['type' => 'str', 'required' => true],
				'OCM_REDES' => ['type' => 'str'],
				'OCM_TIENE_ESTATUTOS' => ['type' => 'bool', 'required' => true],
				'OCM_REGISTRADA_CAMARA_COM' => ['type' => 'bool', 'required' => true],
				'OCM_NUM_RADICADO_CAMARA_COM' => ['type' => 'int', 'min' => 0],
				'OCM_PORC_CUMPL_PLAN_ULT_ANNO' => ['type' => 'bool', 'min' => 0, 'required' => true],
				'OCM_PLAN_DE_TRABAJO' => ['type' => 'str', 'required' => true],
				'OCM_PLAN_DE_TRABAJO_PLA_PUB' => ['type' => 'str', 'required' => true],
				'OCM_PLAN_DE_TRABAJO_POL_PUB' => ['type' => 'str', 'required' => true],
				'INSTRUMENTO_PLANEACION_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'OCM_RECURSOS_ECONOMICOS' => ['type' => 'str', 'max' => 255],
				'OCM_FUENTES_FINANCIACION' => ['type' => 'str', 'max' => 255],
				'OCM_PRESPUESTO_ANUAL' => ['type' => 'str', 'max' => 30, 'required' => true],
				'OCM_RECURSOS_PROPIOS' => ['type' => 'bool', 'required' => true],
				'PR_EJ_REC_PROP_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'PR_EJ_REC_PUB_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'PR_EJ_COOP_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'OCM_REGISTRO_INGRESOS_Y_GASTOS' => ['type' => 'bool', 'required' => true],
				'OCM_OBLIGACIONES_TRIBUTARIAS' => ['type' => 'bool', 'required' => true],
				'OCM_CONTADOR' => ['type' => 'bool', 'required' => true],
				'OCM_ACTIVIDADES_CONJUNTAS_PRIVADAS' => ['type' => 'str', 'max' => 255],
				'OCM_ACTIVIDADES_CONJUNTAS_PUBLICAS' => ['type' => 'str', 'max' => 255],
				'OCM_ACTIVIDADES_CONJUNTAS_OTRAS_ORG' => ['type' => 'str', 'max' => 255],
				'ESCALA_TERRITORIAL_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'COB_TERRITORIAL' => ['type' => 'str', 'max' => 255, 'required' => true],
				'OCM_MEDIOS_PROMOCION' => ['type' => 'str', 'max' => 255, 'required' => true],
				'OCM_LINK_CANAL_EMISORA' => ['type' => 'str', 'max' => 255],
				'OCM_RENDICION_CUENTAS' => ['type' => 'bool', 'required' => true],
				'OCM_ULTIMA_REND_CUENTA' => ['type' => 'str', 'max' => 120, 'required' => true],
				'OCM_ACCIONES_CONTROL_SOCIAL' => ['type' => 'str', 'max' => 255],
				'OCM_NECESIDADES_ASESORIA' => ['type' => 'str', 'max' => 255],
				'OCM_PUNTUACION' => ['type' => 'str', 'max' => 20, 'required' => true],
				'REPRESENTANTE_LEGAL_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'OCM_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> ['COMUNA_ID'],

			'relationships' => [
				'TBL_COMUNAS' => [
					['TBL_COMUNAS.COM_ID','TBL_ORG_COMUNALES.COMUNA_ID']
				],
				'TBL_ORG_COMUNAL_ENTIDAD_REG' => [
					['TBL_ORG_COMUNAL_ENTIDAD_REG.ORG_COMUNAL_ID','TBL_ORG_COMUNALES.ID_OCM']
				]
			],

			'expanded_relationships' => array (
  'TBL_COMUNAS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_COMUNAS',
        1 => 'COM_ID',
      ),
      1 => 
      array (
        0 => 'TBL_ORG_COMUNALES',
        1 => 'COMUNA_ID',
      ),
    ),
  ),
  'TBL_ORG_COMUNAL_ENTIDAD_REG' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ORG_COMUNAL_ENTIDAD_REG',
        1 => 'ORG_COMUNAL_ID',
      ),
      1 => 
      array (
        0 => 'TBL_ORG_COMUNALES',
        1 => 'ID_OCM',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_COMUNAS' => [
					['TBL_COMUNAS.COM_ID','TBL_ORG_COMUNALES.COMUNA_ID']
				]
			],

			'expanded_relationships_from' => array (
  'TBL_COMUNAS' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_COMUNAS',
        1 => 'COM_ID',
      ),
      1 => 
      array (
        0 => 'TBL_ORG_COMUNALES',
        1 => 'COMUNA_ID',
      ),
    ),
  ),
)
		];
	}	
}

