<?php 

$pivots = array (
  'comments,products' => 'product_comments',
  'roles,users' => 'user_roles',
  'sp_permissions,users' => 'user_sp_permissions',
  'products,valoraciones' => 'product_valoraciones',
);

$pivot_fks = array (
  'product_comments' => 
  array (
    'comments' => 'comment_id',
    'products' => 'product_id',
  ),
  'user_roles' => 
  array (
    'users' => 'user_id',
    'roles' => 'role_id',
  ),
  'user_sp_permissions' => 
  array (
    'users' => 'user_id',
    'sp_permissions' => 'sp_permission_id',
  ),
  'product_valoraciones' => 
  array (
    'products' => 'product_id',
    'valoraciones' => 'valoracion_id',
  ),
);

$relationships = array (
  'product_comments' => 
  array (
    'comments' => 
    array (
      0 => 
      array (
        0 => 'comments.id',
        1 => 'product_comments.comment_id',
      ),
    ),
    'products' => 
    array (
      0 => 
      array (
        0 => 'products.id',
        1 => 'product_comments.product_id',
      ),
    ),
  ),
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
    'users' => 
    array (
      0 => 
      array (
        0 => 'users.id',
        1 => 'user_sp_permissions.user_id',
      ),
    ),
    'sp_permissions' => 
    array (
      0 => 
      array (
        0 => 'sp_permissions.id',
        1 => 'user_sp_permissions.sp_permission_id',
      ),
    ),
  ),
  'product_valoraciones' => 
  array (
    'products' => 
    array (
      0 => 
      array (
        0 => 'products.id',
        1 => 'product_valoraciones.product_id',
      ),
    ),
    'valoraciones' => 
    array (
      0 => 
      array (
        0 => 'valoraciones.id_val',
        1 => 'product_valoraciones.valoracion_id',
      ),
    ),
  ),
);