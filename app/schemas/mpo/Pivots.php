<?php 

$pivots = array (
  'entidades_registrantes,org_comunales' => 'org_comunal_entidad_registrante',
);

$pivot_fks = array (
  'org_comunal_entidad_registrante' => 
  array (
    'entidades_registrantes' => 'entidad_registrante_id',
    'org_comunales' => 'org_comunal_id',
  ),
);

$relationships = array (
  'org_comunal_entidad_registrante' => 
  array (
    'entidades_registrantes' => 
    array (
      0 => 
      array (
        0 => 'entidades_registrantes.id',
        1 => 'org_comunal_entidad_registrante.entidad_registrante_id',
      ),
    ),
    'org_comunales' => 
    array (
      0 => 
      array (
        0 => 'org_comunales.id',
        1 => 'org_comunal_entidad_registrante.org_comunal_id',
      ),
    ),
  ),
);
