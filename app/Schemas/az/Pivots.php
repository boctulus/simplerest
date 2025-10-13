<?php 

$pivots = array (
  'product_categories,products' => 'products_product_categories',
  'sp_permissions,users' => 'user_sp_permissions',
);

$pivot_fks = array (
  'products_product_categories' => 
  array (
    'product_categories' => 'product_category_id',
    'products' => 'product_id',
  ),
  'user_sp_permissions' => 
  array (
    'sp_permissions' => 'sp_permission_id',
    'users' => 'user_id',
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
