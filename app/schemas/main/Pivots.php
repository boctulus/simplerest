<?php 

$pivots = array (
  'roles,users' => 'user_roles',
  'sp_permissions,users' => 'user_sp_permissions',
);

$pivot_fks = array (
  'user_roles' => 
  array (
    'roles' => 'role_id',
    'users' => 'user_id',
  ),
  'user_sp_permissions' => 
  array (
    'sp_permissions' => 'sp_permission_id',
    'users' => 'user_id',
  ),
);

$relationships = array (
  'user_roles' => 
  array (
    'roles' => 
    array (
      0 => 
      array (
        0 => 'roles.id',
        1 => 'user_roles.role_id',
      ),
    ),
    'users' => 
    array (
      0 => 
      array (
        0 => 'users.id',
        1 => 'user_roles.user_id',
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
    'users' => 
    array (
      0 => 
      array (
        0 => 'users.id',
        1 => 'user_sp_permissions.user_id',
      ),
    ),
  ),
);
