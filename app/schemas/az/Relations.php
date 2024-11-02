<?php 

return [
        'related_tables' => array (
  'api_keys' => 
  array (
    0 => 'users',
  ),
  'automoviles' => 
  array (
    0 => 'medios_transporte',
  ),
  'boletas' => 
  array (
    0 => 'users',
  ),
  'book_reviews' => 
  array (
    0 => 'books',
  ),
  'books' => 
  array (
    0 => 'users',
    1 => 'book_reviews',
  ),
  'collections' => 
  array (
    0 => 'users',
  ),
  'facturas' => 
  array (
    0 => 'users',
  ),
  'facturas4' => 
  array (
    0 => 'users',
  ),
  'files' => 
  array (
    0 => 'users',
  ),
  'folder_other_permissions' => 
  array (
    0 => 'users',
  ),
  'folder_permissions' => 
  array (
    0 => 'users',
  ),
  'folders' => 
  array (
    0 => 'users',
  ),
  'medios_transporte' => 
  array (
    0 => 'automoviles',
  ),
  'product_categories' => 
  array (
    0 => 'products_product_categories',
  ),
  'product_comments' => 
  array (
    0 => 'products',
  ),
  'products' => 
  array (
    0 => 'users',
    1 => 'product_comments',
    2 => 'products_product_categories',
  ),
  'products_product_categories' => 
  array (
    0 => 'products',
    1 => 'product_categories',
  ),
  'sp_permissions' => 
  array (
    0 => 'user_sp_permissions',
  ),
  'user_roles' => 
  array (
    0 => 'users',
  ),
  'user_sp_permissions' => 
  array (
    0 => 'sp_permissions',
    1 => 'users',
  ),
  'user_tb_permissions' => 
  array (
    0 => 'users',
  ),
  'users' => 
  array (
    0 => 'api_keys',
    1 => 'boletas',
    2 => 'books',
    3 => 'collections',
    4 => 'facturas',
    5 => 'facturas4',
    6 => 'files',
    7 => 'folder_other_permissions',
    8 => 'folder_permissions',
    9 => 'folders',
    10 => 'products',
    11 => 'user_roles',
    12 => 'user_sp_permissions',
    13 => 'user_tb_permissions',
  ),
),
        'relation_type'  => array (
  'api_keys~users' => 'n:1',
  'automoviles~medios_transporte' => '1:n',
  'boletas~users' => 'n:1',
  'book_reviews~books' => 'n:1',
  'books~users' => 'n:1',
  'books~book_reviews' => '1:n',
  'collections~users' => 'n:1',
  'facturas~users' => 'n:1',
  'facturas4~users' => 'n:1',
  'files~users' => 'n:1',
  'folder_other_permissions~users' => 'n:1',
  'folder_permissions~users' => 'n:1',
  'folders~users' => 'n:1',
  'medios_transporte~automoviles' => 'n:1',
  'product_categories~products_product_categories' => '1:n',
  'product_comments~products' => '1:1',
  'products~users' => 'n:1',
  'products~product_comments' => '1:1',
  'products~products_product_categories' => '1:n',
  'products_product_categories~products' => 'n:1',
  'products_product_categories~product_categories' => 'n:1',
  'sp_permissions~user_sp_permissions' => '1:n',
  'user_roles~users' => 'n:1',
  'user_sp_permissions~sp_permissions' => 'n:1',
  'user_sp_permissions~users' => 'n:1',
  'user_tb_permissions~users' => 'n:1',
  'users~api_keys' => '1:n',
  'users~boletas' => '1:n',
  'users~books' => '1:n',
  'users~collections' => '1:n',
  'users~facturas' => '1:n',
  'users~facturas4' => '1:n',
  'users~files' => '1:n',
  'users~folder_other_permissions' => '1:n',
  'users~folder_permissions' => '1:n',
  'users~folders' => '1:n',
  'users~products' => '1:n',
  'users~user_roles' => '1:n',
  'users~user_sp_permissions' => '1:n',
  'users~user_tb_permissions' => '1:n',
  'product_categories~products' => 'n:m',
  'products~product_categories' => 'n:m',
  'sp_permissions~users' => 'n:m',
  'users~sp_permissions' => 'n:m',
),
        'multiplicity'   => array (
  'api_keys~users' => false,
  'automoviles~medios_transporte' => true,
  'boletas~users' => false,
  'book_reviews~books' => false,
  'books~users' => false,
  'books~book_reviews' => true,
  'collections~users' => false,
  'facturas~users' => false,
  'facturas4~users' => false,
  'files~users' => false,
  'folder_other_permissions~users' => false,
  'folder_permissions~users' => false,
  'folders~users' => false,
  'medios_transporte~automoviles' => false,
  'product_categories~products_product_categories' => true,
  'product_comments~products' => false,
  'products~users' => false,
  'products~product_comments' => false,
  'products~products_product_categories' => true,
  'products_product_categories~products' => false,
  'products_product_categories~product_categories' => false,
  'sp_permissions~user_sp_permissions' => true,
  'user_roles~users' => false,
  'user_sp_permissions~sp_permissions' => false,
  'user_sp_permissions~users' => false,
  'user_tb_permissions~users' => false,
  'users~api_keys' => true,
  'users~boletas' => true,
  'users~books' => true,
  'users~collections' => true,
  'users~facturas' => true,
  'users~facturas4' => true,
  'users~files' => true,
  'users~folder_other_permissions' => true,
  'users~folder_permissions' => true,
  'users~folders' => true,
  'users~products' => true,
  'users~user_roles' => true,
  'users~user_sp_permissions' => true,
  'users~user_tb_permissions' => true,
  'product_categories~products' => true,
  'products~product_categories' => true,
  'sp_permissions~users' => true,
  'users~sp_permissions' => true,
),
];