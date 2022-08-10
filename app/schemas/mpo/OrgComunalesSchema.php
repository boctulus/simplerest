<?php

namespace simplerest\schemas\mpo;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class OrgComunalesSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'org_comunales',

			'id_name'		=> 'id',

			'fields'		=> ['id', 'nombre_org_comunal', 'fecha_aplicacion', 'zona', 'comuna_corregimiento', 'direccion', 'departamento_id', 'subregion_id', 'municipio_id', 'representante_legal_id', 'tipo_organismo_org_comunal_id', 'estado_seguimiento_id', 'institucion_avalante', 'sector_actividad_org_comunal_id', 'objetivo_social', 'certificacion_que_emite_org_comunal_id', 'areas_tematicas', 'fortalezas_organizacion', 'redes', '_tiene_estatutos', '_registrada_camara_com', 'num_radicado_camara_com', 'porc_cumpl_plan_ult_anno', 'plan_de_trabajo', 'plan_de_trabajo_pla_pub', 'plan_de_trabajo_pol_pub', 'instrumento_planeacion_id', 'recursos_economicos', 'fuentes_financiacion', 'prespuesto_anual', '_recursos_propios', 'proyectos_ejecutados_rec_propios', 'proyectos_ejecutados_rec_publicos', 'proyectos_ejecutados_cooperacion', '_registro_ingresos_y_gastos', '_obligaciones_tributarias', '_contador', 'actividades_conjuntas_privadas', 'actividades_conjuntas_publicas', 'actividades_conjuntas_otras_org', 'escala_territorial_id', 'medios_promocion', 'link_canal_emisora', '_rendicion_cuentas', 'ultima_rend_cuenta', 'acciones_control_social', 'necesidades_asesoria', 'puntuacion', 'nivel_id', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'id' => 'INT',
				'nombre_org_comunal' => 'STR',
				'fecha_aplicacion' => 'STR',
				'zona' => 'STR',
				'comuna_corregimiento' => 'STR',
				'direccion' => 'STR',
				'departamento_id' => 'INT',
				'subregion_id' => 'INT',
				'municipio_id' => 'INT',
				'representante_legal_id' => 'INT',
				'tipo_organismo_org_comunal_id' => 'INT',
				'estado_seguimiento_id' => 'INT',
				'institucion_avalante' => 'STR',
				'sector_actividad_org_comunal_id' => 'INT',
				'objetivo_social' => 'STR',
				'certificacion_que_emite_org_comunal_id' => 'INT',
				'areas_tematicas' => 'STR',
				'fortalezas_organizacion' => 'STR',
				'redes' => 'STR',
				'_tiene_estatutos' => 'INT',
				'_registrada_camara_com' => 'INT',
				'num_radicado_camara_com' => 'INT',
				'porc_cumpl_plan_ult_anno' => 'INT',
				'plan_de_trabajo' => 'STR',
				'plan_de_trabajo_pla_pub' => 'STR',
				'plan_de_trabajo_pol_pub' => 'STR',
				'instrumento_planeacion_id' => 'INT',
				'recursos_economicos' => 'STR',
				'fuentes_financiacion' => 'STR',
				'prespuesto_anual' => 'STR',
				'_recursos_propios' => 'INT',
				'proyectos_ejecutados_rec_propios' => 'STR',
				'proyectos_ejecutados_rec_publicos' => 'STR',
				'proyectos_ejecutados_cooperacion' => 'STR',
				'_registro_ingresos_y_gastos' => 'INT',
				'_obligaciones_tributarias' => 'INT',
				'_contador' => 'INT',
				'actividades_conjuntas_privadas' => 'STR',
				'actividades_conjuntas_publicas' => 'STR',
				'actividades_conjuntas_otras_org' => 'STR',
				'escala_territorial_id' => 'INT',
				'medios_promocion' => 'STR',
				'link_canal_emisora' => 'STR',
				'_rendicion_cuentas' => 'INT',
				'ultima_rend_cuenta' => 'STR',
				'acciones_control_social' => 'STR',
				'necesidades_asesoria' => 'STR',
				'puntuacion' => 'STR',
				'nivel_id' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['id'],

			'autoincrement' => 'id',

			'nullable'		=> ['id', 'zona', 'institucion_avalante', 'redes', 'num_radicado_camara_com', 'recursos_economicos', 'fuentes_financiacion', 'proyectos_ejecutados_rec_propios', 'proyectos_ejecutados_rec_publicos', 'proyectos_ejecutados_cooperacion', 'actividades_conjuntas_privadas', 'actividades_conjuntas_publicas', 'actividades_conjuntas_otras_org', 'link_canal_emisora', 'acciones_control_social', 'necesidades_asesoria', 'created_at', 'updated_at'],

			'required'		=> ['nombre_org_comunal', 'fecha_aplicacion', 'comuna_corregimiento', 'direccion', 'departamento_id', 'subregion_id', 'municipio_id', 'representante_legal_id', 'tipo_organismo_org_comunal_id', 'estado_seguimiento_id', 'sector_actividad_org_comunal_id', 'objetivo_social', 'certificacion_que_emite_org_comunal_id', 'areas_tematicas', 'fortalezas_organizacion', '_tiene_estatutos', '_registrada_camara_com', 'porc_cumpl_plan_ult_anno', 'plan_de_trabajo', 'plan_de_trabajo_pla_pub', 'plan_de_trabajo_pol_pub', 'instrumento_planeacion_id', 'prespuesto_anual', '_recursos_propios', '_registro_ingresos_y_gastos', '_obligaciones_tributarias', '_contador', 'escala_territorial_id', 'medios_promocion', '_rendicion_cuentas', 'ultima_rend_cuenta', 'puntuacion', 'nivel_id'],

			'uniques'		=> [],

			'rules' 		=> [
				'id' => ['type' => 'int', 'min' => 0],
				'nombre_org_comunal' => ['type' => 'str', 'max' => 60, 'required' => true],
				'fecha_aplicacion' => ['type' => 'date', 'required' => true],
				'zona' => ['type' => 'str', 'max' => 30],
				'comuna_corregimiento' => ['type' => 'str', 'max' => 50, 'required' => true],
				'direccion' => ['type' => 'str', 'max' => 255, 'required' => true],
				'departamento_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'subregion_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'municipio_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'representante_legal_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'tipo_organismo_org_comunal_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'estado_seguimiento_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'institucion_avalante' => ['type' => 'str'],
				'sector_actividad_org_comunal_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'objetivo_social' => ['type' => 'str', 'max' => 255, 'required' => true],
				'certificacion_que_emite_org_comunal_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'areas_tematicas' => ['type' => 'str', 'required' => true],
				'fortalezas_organizacion' => ['type' => 'str', 'required' => true],
				'redes' => ['type' => 'str'],
				'_tiene_estatutos' => ['type' => 'bool', 'required' => true],
				'_registrada_camara_com' => ['type' => 'bool', 'required' => true],
				'num_radicado_camara_com' => ['type' => 'int'],
				'porc_cumpl_plan_ult_anno' => ['type' => 'bool', 'min' => 0, 'required' => true],
				'plan_de_trabajo' => ['type' => 'str', 'required' => true],
				'plan_de_trabajo_pla_pub' => ['type' => 'str', 'required' => true],
				'plan_de_trabajo_pol_pub' => ['type' => 'str', 'required' => true],
				'instrumento_planeacion_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'recursos_economicos' => ['type' => 'str', 'max' => 255],
				'fuentes_financiacion' => ['type' => 'str', 'max' => 255],
				'prespuesto_anual' => ['type' => 'str', 'max' => 30, 'required' => true],
				'_recursos_propios' => ['type' => 'bool', 'required' => true],
				'proyectos_ejecutados_rec_propios' => ['type' => 'str'],
				'proyectos_ejecutados_rec_publicos' => ['type' => 'str'],
				'proyectos_ejecutados_cooperacion' => ['type' => 'str'],
				'_registro_ingresos_y_gastos' => ['type' => 'bool', 'required' => true],
				'_obligaciones_tributarias' => ['type' => 'bool', 'required' => true],
				'_contador' => ['type' => 'bool', 'required' => true],
				'actividades_conjuntas_privadas' => ['type' => 'str', 'max' => 255],
				'actividades_conjuntas_publicas' => ['type' => 'str', 'max' => 255],
				'actividades_conjuntas_otras_org' => ['type' => 'str', 'max' => 255],
				'escala_territorial_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'medios_promocion' => ['type' => 'str', 'max' => 255, 'required' => true],
				'link_canal_emisora' => ['type' => 'str', 'max' => 255],
				'_rendicion_cuentas' => ['type' => 'bool', 'required' => true],
				'ultima_rend_cuenta' => ['type' => 'str', 'max' => 120, 'required' => true],
				'acciones_control_social' => ['type' => 'str', 'max' => 255],
				'necesidades_asesoria' => ['type' => 'str', 'max' => 255],
				'puntuacion' => ['type' => 'str', 'max' => 20, 'required' => true],
				'nivel_id' => ['type' => 'int', 'min' => 0, 'required' => true],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> ['certificacion_que_emite_org_comunal_id', 'departamento_id', 'escala_territorial_id', 'estado_seguimiento_id', 'instrumento_planeacion_id', 'municipio_id', 'nivel_id', 'representante_legal_id', 'sector_actividad_org_comunal_id', 'subregion_id', 'tipo_organismo_org_comunal_id'],

			'relationships' => [
				'certificaciones_que_emite_org_comunal' => [
					['certificaciones_que_emite_org_comunal.id','org_comunales.certificacion_que_emite_org_comunal_id']
				],
				'departamentos' => [
					['departamentos.id','org_comunales.departamento_id']
				],
				'escalas_territoriales' => [
					['escalas_territoriales.id','org_comunales.escala_territorial_id']
				],
				'estados_seguimiento' => [
					['estados_seguimiento.id','org_comunales.estado_seguimiento_id']
				],
				'instrumentos_planeacion' => [
					['instrumentos_planeacion.id','org_comunales.instrumento_planeacion_id']
				],
				'municipios' => [
					['municipios.id','org_comunales.municipio_id']
				],
				'niveles' => [
					['niveles.id','org_comunales.nivel_id']
				],
				'representantes_legales' => [
					['representantes_legales.id','org_comunales.representante_legal_id']
				],
				'sectores_actividad_org_comunal' => [
					['sectores_actividad_org_comunal.id','org_comunales.sector_actividad_org_comunal_id']
				],
				'subregiones' => [
					['subregiones.id','org_comunales.subregion_id']
				],
				'tipos_organismos_org_comunales' => [
					['tipos_organismos_org_comunales.id','org_comunales.tipo_organismo_org_comunal_id']
				],
				'org_comunal_entidad_registrante' => [
					['org_comunal_entidad_registrante.org_comunal_id','org_comunales.id']
				]
			],

			'expanded_relationships' => array (
  'certificaciones_que_emite_org_comunal' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'certificaciones_que_emite_org_comunal',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'certificacion_que_emite_org_comunal_id',
      ),
    ),
  ),
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
        0 => 'org_comunales',
        1 => 'departamento_id',
      ),
    ),
  ),
  'escalas_territoriales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'escalas_territoriales',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'escala_territorial_id',
      ),
    ),
  ),
  'estados_seguimiento' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'estados_seguimiento',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'estado_seguimiento_id',
      ),
    ),
  ),
  'instrumentos_planeacion' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'instrumentos_planeacion',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'instrumento_planeacion_id',
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
        0 => 'org_comunales',
        1 => 'municipio_id',
      ),
    ),
  ),
  'niveles' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'niveles',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'nivel_id',
      ),
    ),
  ),
  'representantes_legales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'representantes_legales',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'representante_legal_id',
      ),
    ),
  ),
  'sectores_actividad_org_comunal' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'sectores_actividad_org_comunal',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'sector_actividad_org_comunal_id',
      ),
    ),
  ),
  'subregiones' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'subregiones',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'subregion_id',
      ),
    ),
  ),
  'tipos_organismos_org_comunales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'tipos_organismos_org_comunales',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'tipo_organismo_org_comunal_id',
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
        1 => 'org_comunal_id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'id',
      ),
    ),
  ),
),

			'relationships_from' => [
				'certificaciones_que_emite_org_comunal' => [
					['certificaciones_que_emite_org_comunal.id','org_comunales.certificacion_que_emite_org_comunal_id']
				],
				'departamentos' => [
					['departamentos.id','org_comunales.departamento_id']
				],
				'escalas_territoriales' => [
					['escalas_territoriales.id','org_comunales.escala_territorial_id']
				],
				'estados_seguimiento' => [
					['estados_seguimiento.id','org_comunales.estado_seguimiento_id']
				],
				'instrumentos_planeacion' => [
					['instrumentos_planeacion.id','org_comunales.instrumento_planeacion_id']
				],
				'municipios' => [
					['municipios.id','org_comunales.municipio_id']
				],
				'niveles' => [
					['niveles.id','org_comunales.nivel_id']
				],
				'representantes_legales' => [
					['representantes_legales.id','org_comunales.representante_legal_id']
				],
				'sectores_actividad_org_comunal' => [
					['sectores_actividad_org_comunal.id','org_comunales.sector_actividad_org_comunal_id']
				],
				'subregiones' => [
					['subregiones.id','org_comunales.subregion_id']
				],
				'tipos_organismos_org_comunales' => [
					['tipos_organismos_org_comunales.id','org_comunales.tipo_organismo_org_comunal_id']
				]
			],

			'expanded_relationships_from' => array (
  'certificaciones_que_emite_org_comunal' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'certificaciones_que_emite_org_comunal',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'certificacion_que_emite_org_comunal_id',
      ),
    ),
  ),
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
        0 => 'org_comunales',
        1 => 'departamento_id',
      ),
    ),
  ),
  'escalas_territoriales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'escalas_territoriales',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'escala_territorial_id',
      ),
    ),
  ),
  'estados_seguimiento' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'estados_seguimiento',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'estado_seguimiento_id',
      ),
    ),
  ),
  'instrumentos_planeacion' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'instrumentos_planeacion',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'instrumento_planeacion_id',
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
        0 => 'org_comunales',
        1 => 'municipio_id',
      ),
    ),
  ),
  'niveles' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'niveles',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'nivel_id',
      ),
    ),
  ),
  'representantes_legales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'representantes_legales',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'representante_legal_id',
      ),
    ),
  ),
  'sectores_actividad_org_comunal' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'sectores_actividad_org_comunal',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'sector_actividad_org_comunal_id',
      ),
    ),
  ),
  'subregiones' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'subregiones',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'subregion_id',
      ),
    ),
  ),
  'tipos_organismos_org_comunales' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'tipos_organismos_org_comunales',
        1 => 'id',
      ),
      1 => 
      array (
        0 => 'org_comunales',
        1 => 'tipo_organismo_org_comunal_id',
      ),
    ),
  ),
)
		];
	}	
}

