<?php

namespace simplerest\schemas\mpp;

use simplerest\core\interfaces\ISchema;

### IMPORTS

class TBL_ORG_COMUNAL_ENTIDAD_REGSchema implements ISchema
{ 
	static function get(){
		return [
			'table_name'	=> 'TBL_ORG_COMUNAL_ENTIDAD_REG',

			'id_name'		=> 'ID_OER',

			'fields'		=> ['ID_OER', 'TIPO_VINCULO_ID', 'OER_CANT_PERSONAS', 'ORG_COMUNAL_ID', 'ENTIDAD_REG_ID', 'OER_BORRADO', 'created_at', 'updated_at'],

			'attr_types'	=> [
				'ID_OER' => 'INT',
				'TIPO_VINCULO_ID' => 'INT',
				'OER_CANT_PERSONAS' => 'INT',
				'ORG_COMUNAL_ID' => 'INT',
				'ENTIDAD_REG_ID' => 'INT',
				'OER_BORRADO' => 'INT',
				'created_at' => 'STR',
				'updated_at' => 'STR'
			],

			'primary'		=> ['ID_OER'],

			'autoincrement' => 'ID_OER',

			'nullable'		=> ['ID_OER', 'OER_BORRADO', 'created_at', 'updated_at'],

			'required'		=> ['TIPO_VINCULO_ID', 'OER_CANT_PERSONAS', 'ORG_COMUNAL_ID', 'ENTIDAD_REG_ID'],

			'uniques'		=> [],

			'rules' 		=> [
				'ID_OER' => ['type' => 'int', 'min' => 0],
				'TIPO_VINCULO_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'OER_CANT_PERSONAS' => ['type' => 'int', 'min' => 0, 'required' => true],
				'ORG_COMUNAL_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'ENTIDAD_REG_ID' => ['type' => 'int', 'min' => 0, 'required' => true],
				'OER_BORRADO' => ['type' => 'bool'],
				'created_at' => ['type' => 'timestamp'],
				'updated_at' => ['type' => 'timestamp']
			],

			'fks' 			=> ['ENTIDAD_REG_ID', 'ORG_COMUNAL_ID', 'TIPO_VINCULO_ID'],

			'relationships' => [
				'TBL_ENTIDADES_REG' => [
					['TBL_ENTIDADES_REG.ID_ERG','TBL_ORG_COMUNAL_ENTIDAD_REG.ENTIDAD_REG_ID']
				],
				'TBL_ORG_COMUNALES' => [
					['TBL_ORG_COMUNALES.ID_OCM','TBL_ORG_COMUNAL_ENTIDAD_REG.ORG_COMUNAL_ID']
				],
				'TBL_TIPO_VINCULO_OER' => [
					['TBL_TIPO_VINCULO_OER.ID_TVO','TBL_ORG_COMUNAL_ENTIDAD_REG.TIPO_VINCULO_ID']
				]
			],

			'expanded_relationships' => array (
  'TBL_ENTIDADES_REG' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ENTIDADES_REG',
        1 => 'ID_ERG',
      ),
      1 => 
      array (
        0 => 'TBL_ORG_COMUNAL_ENTIDAD_REG',
        1 => 'ENTIDAD_REG_ID',
      ),
    ),
  ),
  'TBL_ORG_COMUNALES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ORG_COMUNALES',
        1 => 'ID_OCM',
      ),
      1 => 
      array (
        0 => 'TBL_ORG_COMUNAL_ENTIDAD_REG',
        1 => 'ORG_COMUNAL_ID',
      ),
    ),
  ),
  'TBL_TIPO_VINCULO_OER' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_TIPO_VINCULO_OER',
        1 => 'ID_TVO',
      ),
      1 => 
      array (
        0 => 'TBL_ORG_COMUNAL_ENTIDAD_REG',
        1 => 'TIPO_VINCULO_ID',
      ),
    ),
  ),
),

			'relationships_from' => [
				'TBL_ENTIDADES_REG' => [
					['TBL_ENTIDADES_REG.ID_ERG','TBL_ORG_COMUNAL_ENTIDAD_REG.ENTIDAD_REG_ID']
				],
				'TBL_ORG_COMUNALES' => [
					['TBL_ORG_COMUNALES.ID_OCM','TBL_ORG_COMUNAL_ENTIDAD_REG.ORG_COMUNAL_ID']
				],
				'TBL_TIPO_VINCULO_OER' => [
					['TBL_TIPO_VINCULO_OER.ID_TVO','TBL_ORG_COMUNAL_ENTIDAD_REG.TIPO_VINCULO_ID']
				]
			],

			'expanded_relationships_from' => array (
  'TBL_ENTIDADES_REG' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ENTIDADES_REG',
        1 => 'ID_ERG',
      ),
      1 => 
      array (
        0 => 'TBL_ORG_COMUNAL_ENTIDAD_REG',
        1 => 'ENTIDAD_REG_ID',
      ),
    ),
  ),
  'TBL_ORG_COMUNALES' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_ORG_COMUNALES',
        1 => 'ID_OCM',
      ),
      1 => 
      array (
        0 => 'TBL_ORG_COMUNAL_ENTIDAD_REG',
        1 => 'ORG_COMUNAL_ID',
      ),
    ),
  ),
  'TBL_TIPO_VINCULO_OER' => 
  array (
    0 => 
    array (
      0 => 
      array (
        0 => 'TBL_TIPO_VINCULO_OER',
        1 => 'ID_TVO',
      ),
      1 => 
      array (
        0 => 'TBL_ORG_COMUNAL_ENTIDAD_REG',
        1 => 'TIPO_VINCULO_ID',
      ),
    ),
  ),
)
		];
	}	
}

