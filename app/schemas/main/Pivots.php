<?php 

$pivots = array (
  'roles,users' => 'user_roles',
  'sp_permissions,tbl_usuario_empresa' => 'user_sp_permissions',
);

$pivot_fks = array (
  'user_roles' => 
  array (
    'users' => 'user_id',
    'roles' => 'role_id',
  ),
  'user_sp_permissions' => 
  array (
    'sp_permissions' => 'sp_permission_id',
    'tbl_usuario_empresa' => 'user_id',
  ),
);

$relationships = array (
  'user_roles' => 
  array (
    'users' => 
    array (
      0 => 
      array (
        0 => 'users.id',
        1 => 'user_roles.user_id',
      ),
    ),
    'roles' => 
    array (
      0 => 
      array (
        0 => 'roles.id',
        1 => 'user_roles.role_id',
      ),
    ),
  ),
  'user_sp_permissions' => 
  array (
    'sp_permissions' => 
    array (
      0 => 
      array (
        0 => 'sp_permissions.id',
        1 => 'user_sp_permissions.sp_permission_id',
      ),
    ),
    'tbl_usuario_empresa' => 
    array (
      0 => 
      array (
        0 => 'tbl_usuario_empresa.use_intId',
        1 => 'user_sp_permissions.user_id',
      ),
    ),
  ),
);
