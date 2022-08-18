<?php 

$pivots = array (
  'TBL_ENTIDADES_REG,TBL_ORG_COMUNALES' => 'TBL_ORG_COMUNAL_ENTIDAD_REG',
);

$pivot_fks = array (
  'TBL_ORG_COMUNAL_ENTIDAD_REG' => 
  array (
    'TBL_ENTIDADES_REG' => 'ENTIDAD_REG_ID',
    'TBL_ORG_COMUNALES' => 'ERG_ORG_COMUNAL_ID',
  ),
);

$relationships = array (
  'TBL_ORG_COMUNAL_ENTIDAD_REG' => 
  array (
    'TBL_ENTIDADES_REG' => 
    array (
      0 => 
      array (
        0 => 'TBL_ENTIDADES_REG.ID_ERG',
        1 => 'TBL_ORG_COMUNAL_ENTIDAD_REG.ENTIDAD_REG_ID',
      ),
    ),
    'TBL_ORG_COMUNALES' => 
    array (
      0 => 
      array (
        0 => 'TBL_ORG_COMUNALES.ID_OCM',
        1 => 'TBL_ORG_COMUNAL_ENTIDAD_REG.ERG_ORG_COMUNAL_ID',
      ),
    ),
  ),
);
