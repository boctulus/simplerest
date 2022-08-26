<?php 

$pivots = array (
  'TBL_TEMATICAS,TBL_USUARIOS' => 'TBL_ELECCIONES',
  'TBL_ENTIDADES_REG,TBL_ORG_COMUNALES' => 'TBL_ORG_COMUNAL_ENTIDAD_REG',
  'TBL_DEBATES,TBL_USUARIOS' => 'TBL_PARTICIPACION_DEBATE',
  'TBL_ELECCIONES,TBL_USUARIOS' => 'TBL_PARTICIPACION_ELECCION',
  'TBL_PROPUESTAS,TBL_USUARIOS' => 'TBL_PARTICIPACION_PROPUESTA',
  'TBL_SONDEOS,TBL_USUARIOS' => 'TBL_PARTICIPACION_SONDEO',
  'TBL_PERFILES,TBL_ROLES' => 'TBL_PERFILES_ROLES',
  'TBL_PERMISOS,TBL_ROLES' => 'TBL_PERMISOS_ROLES',
  'TBL_GRUPOS_POBLACIONALES,TBL_USUARIOS' => 'TBL_USUARIOS_GRUPOS_POBLACIONALES',
);

$pivot_fks = array (
  'TBL_ELECCIONES' => 
  array (
    'TBL_TEMATICAS' => 'TEM_ID',
    'TBL_USUARIOS' => 'USU_ID',
  ),
  'TBL_ORG_COMUNAL_ENTIDAD_REG' => 
  array (
    'TBL_ENTIDADES_REG' => 'ENTIDAD_REG_ID',
    'TBL_ORG_COMUNALES' => 'ORG_COMUNAL_ID',
  ),
  'TBL_PARTICIPACION_DEBATE' => 
  array (
    'TBL_DEBATES' => 'DEB_ID',
    'TBL_USUARIOS' => 'USU_ID',
  ),
  'TBL_PARTICIPACION_ELECCION' => 
  array (
    'TBL_ELECCIONES' => 'ELE_ID',
    'TBL_USUARIOS' => 'USU_ID',
  ),
  'TBL_PARTICIPACION_PROPUESTA' => 
  array (
    'TBL_PROPUESTAS' => 'PRO_ID',
    'TBL_USUARIOS' => 'USU_ID',
  ),
  'TBL_PARTICIPACION_SONDEO' => 
  array (
    'TBL_SONDEOS' => 'SON_ID',
    'TBL_USUARIOS' => 'USU_ID',
  ),
  'TBL_PERFILES_ROLES' => 
  array (
    'TBL_PERFILES' => 'PERF_ID',
    'TBL_ROLES' => 'ROL_ID',
  ),
  'TBL_PERMISOS_ROLES' => 
  array (
    'TBL_PERMISOS' => 'ID_PERMISO',
    'TBL_ROLES' => 'ID_ROL',
  ),
  'TBL_USUARIOS_GRUPOS_POBLACIONALES' => 
  array (
    'TBL_GRUPOS_POBLACIONALES' => 'GRU_ID',
    'TBL_USUARIOS' => 'USU_ID',
  ),
);

$relationships = array (
  'TBL_ELECCIONES' => 
  array (
    'TBL_TEMATICAS' => 
    array (
      0 => 
      array (
        0 => 'TBL_TEMATICAS.TEM_ID',
        1 => 'TBL_ELECCIONES.TEM_ID',
      ),
    ),
    'TBL_USUARIOS' => 
    array (
      0 => 
      array (
        0 => 'TBL_USUARIOS.USU_ID',
        1 => 'TBL_ELECCIONES.USU_ID',
      ),
    ),
  ),
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
        1 => 'TBL_ORG_COMUNAL_ENTIDAD_REG.ORG_COMUNAL_ID',
      ),
    ),
  ),
  'TBL_PARTICIPACION_DEBATE' => 
  array (
    'TBL_DEBATES' => 
    array (
      0 => 
      array (
        0 => 'TBL_DEBATES.DEB_ID',
        1 => 'TBL_PARTICIPACION_DEBATE.DEB_ID',
      ),
    ),
    'TBL_USUARIOS' => 
    array (
      0 => 
      array (
        0 => 'TBL_USUARIOS.USU_ID',
        1 => 'TBL_PARTICIPACION_DEBATE.USU_ID',
      ),
    ),
  ),
  'TBL_PARTICIPACION_ELECCION' => 
  array (
    'TBL_ELECCIONES' => 
    array (
      0 => 
      array (
        0 => 'TBL_ELECCIONES.ELE_ID',
        1 => 'TBL_PARTICIPACION_ELECCION.ELE_ID',
      ),
    ),
    'TBL_USUARIOS' => 
    array (
      0 => 
      array (
        0 => 'TBL_USUARIOS.USU_ID',
        1 => 'TBL_PARTICIPACION_ELECCION.USU_ID',
      ),
    ),
  ),
  'TBL_PARTICIPACION_PROPUESTA' => 
  array (
    'TBL_PROPUESTAS' => 
    array (
      0 => 
      array (
        0 => 'TBL_PROPUESTAS.PRO_ID',
        1 => 'TBL_PARTICIPACION_PROPUESTA.PRO_ID',
      ),
    ),
    'TBL_USUARIOS' => 
    array (
      0 => 
      array (
        0 => 'TBL_USUARIOS.USU_ID',
        1 => 'TBL_PARTICIPACION_PROPUESTA.USU_ID',
      ),
    ),
  ),
  'TBL_PARTICIPACION_SONDEO' => 
  array (
    'TBL_SONDEOS' => 
    array (
      0 => 
      array (
        0 => 'TBL_SONDEOS.SON_ID',
        1 => 'TBL_PARTICIPACION_SONDEO.SON_ID',
      ),
    ),
    'TBL_USUARIOS' => 
    array (
      0 => 
      array (
        0 => 'TBL_USUARIOS.USU_ID',
        1 => 'TBL_PARTICIPACION_SONDEO.USU_ID',
      ),
    ),
  ),
  'TBL_PERFILES_ROLES' => 
  array (
    'TBL_PERFILES' => 
    array (
      0 => 
      array (
        0 => 'TBL_PERFILES.PER_ID',
        1 => 'TBL_PERFILES_ROLES.PERF_ID',
      ),
    ),
    'TBL_ROLES' => 
    array (
      0 => 
      array (
        0 => 'TBL_ROLES.ROL_ID',
        1 => 'TBL_PERFILES_ROLES.ROL_ID',
      ),
    ),
  ),
  'TBL_PERMISOS_ROLES' => 
  array (
    'TBL_PERMISOS' => 
    array (
      0 => 
      array (
        0 => 'TBL_PERMISOS.PER_ID',
        1 => 'TBL_PERMISOS_ROLES.ID_PERMISO',
      ),
    ),
    'TBL_ROLES' => 
    array (
      0 => 
      array (
        0 => 'TBL_ROLES.ROL_ID',
        1 => 'TBL_PERMISOS_ROLES.ID_ROL',
      ),
    ),
  ),
  'TBL_USUARIOS_GRUPOS_POBLACIONALES' => 
  array (
    'TBL_GRUPOS_POBLACIONALES' => 
    array (
      0 => 
      array (
        0 => 'TBL_GRUPOS_POBLACIONALES.GRU_ID',
        1 => 'TBL_USUARIOS_GRUPOS_POBLACIONALES.GRU_ID',
      ),
    ),
    'TBL_USUARIOS' => 
    array (
      0 => 
      array (
        0 => 'TBL_USUARIOS.USU_ID',
        1 => 'TBL_USUARIOS_GRUPOS_POBLACIONALES.USU_ID',
      ),
    ),
  ),
);
