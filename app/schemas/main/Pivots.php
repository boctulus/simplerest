<?php 

$pivots        = array (
  'product_categories,products' => 'products_product_categories',
  'roles,users' => 'user_roles',
  'sp_permissions,users' => 'user_sp_permissions',
);

$pivot_fks     = array (
  'products_product_categories' => 
  array (
    'product_categories' => 'product_category_id',
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
);

$relationships = array (
  'products_product_categories' => 
  array (
    'product_categories' => 
    array (
      0 => 
      array (
        0 => 'product_categories.id',
        1 => 'products_product_categories.product_category_id',
      ),
    ),
    'products' => 
    array (
      0 => 
      array (
        0 => 'products.id',
        1 => 'products_product_categories.product_id',
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
);
