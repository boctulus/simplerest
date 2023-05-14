<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_RENDICION_CUENTASSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_RENDICION_CUENTAS',

			'id_name'		=> 'REN_ID',

			'fields'		=> ['REN_ID', 'REN_TIPO_ORGANIZACION', 'REN_NOMBRE_ORGANIZACION', 'REN_IMAGEN', 'REN_VIDEO', 'REN_COMUNA', 'REN_BARRIO', 'REN_DIRECCION', 'REN_LINK', 'REN_FECHA', 'REN_HORA', 'REN_ORGANIZACION_FORMAL', 'REN_METODOLOGIA', 'REN_INFORME_CORPORATIVO', 'REN_INFORME_ADMINISTRATIVO', 'REN_INFORME_JURIDICO', 'REN_INFORME_ECONOMICO', 'REN_INFORME_FISCAL', 'REN_PLAN_TRABAJO', 'REN_OTROS_MEDIOS', 'created_at', 'updated_at', 'REN_BORRADO'],

			'attr_types'	=> [
				'REN_ID' => 'INT',
				'REN_TIPO_ORGANIZACION' => 'STR',
				'REN_NOMBRE_ORGANIZACION' => 'STR',
				'REN_IMAGEN' => 'STR',
				'REN_VIDEO' => 'STR',
				'REN_COMUNA' => 'STR',
				'REN_BARRIO' => 'STR',
				'REN_DIRECCION' => 'STR',
				'REN_LINK' => 'STR',
				'REN_FECHA' => 'STR',
				'REN_HORA' => 'STR',
				'REN_ORGANIZACION_FORMAL' => 'STR',
				'REN_METODOLOGIA' => 'STR',
				'REN_INFORME_CORPORATIVO' => 'STR',
				'REN_INFORME_ADMINISTRATIVO' => 'STR',
				'REN_INFORME_JURIDICO' => 'STR',
				'REN_INFORME_ECONOMICO' => 'STR',
				'REN_INFORME_FISCAL' => 'STR',
				'REN_PLAN_TRABAJO' => 'STR',
				'REN_OTROS_MEDIOS' => 'STR',
				'created_at' => 'STR',
				'updated_at' => 'STR',
				'REN_BORRADO' => 'INT'
			],

			'primary'		=> ['REN_ID'],

			'autoincrement' => 'REN_ID',

			'nullable'		=> ['REN_ID', 'REN_TIPO_ORGANIZACION', 'REN_NOMBRE_ORGANIZACION', 'REN_IMAGEN', 'REN_VIDEO', 'REN_COMUNA', 'REN_BARRIO', 'REN_DIRECCION', 'REN_LINK', 'REN_FECHA', 'REN_HORA', 'REN_ORGANIZACION_FORMAL', 'REN_METODOLOGIA', 'REN_INFORME_CORPORATIVO', 'REN_INFORME_ADMINISTRATIVO', 'REN_INFORME_JURIDICO', 'REN_INFORME_ECONOMICO', 'REN_INFORME_FISCAL', 'REN_PLAN_TRABAJO', 'REN_OTROS_MEDIOS', 'created_at', 'updated_at', 'REN_BORRADO'],

			'required'		=> [],

			'uniques'		=> [],

			'rules' 		=> [
				'REN_ID' => ['type' => 'int'],
				'REN_TIPO_ORGANIZACION' => ['type' => 'str', 'max' => 45],
				'REN_NOMBRE_ORGANIZACION' => ['type' => 'str', 'max' => 45],
				'REN_IMAGEN' => ['type' => 'str', 'max' => 255],
				'REN_VIDEO' => ['type' => 'str', 'max' => 255],
				'REN_COMUNA' => ['type' => 'str', 'max' => 45],
				'REN_BARRIO' => ['type' => 'str', 'max' => 45],
				'REN_DIRECCION' => ['type' => 'str', 'max' => 45],
				'REN_LINK' => ['type' => 'str', 'max' => 45],
				'REN_FECHA' => ['type' => 'date'],
				'REN_HORA' => ['type' => 'time'],
				'REN_ORGANIZACION_FORMAL' => ['type' => 'str', 'max' => 45],
				'REN_METODOLOGIA' => ['type' => 'str'],
				'REN_INFORME_CORPORATIVO' => ['type' => 'str'],
				'REN_INFORME_ADMINISTRATIVO' => ['type' => 'str'],
				'REN_INFORME_JURIDICO' => ['type' => 'str'],
				'REN_INFORME_ECONOMICO' => ['type' => 'str'],
				'REN_INFORME_FISCAL' => ['type' => 'str'],
				'REN_PLAN_TRABAJO' => ['type' => 'str'],
				'REN_OTROS_MEDIOS' => ['type' => 'str'],
				'created_at' => ['type' => 'datetime'],
				'updated_at' => ['type' => 'datetime'],
				'REN_BORRADO' => ['type' => 'bool']
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

