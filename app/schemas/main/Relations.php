<?php 

return [
        'related_tables' => array (
  'api_keys' => 
  array (
    0 => 'users',
  ),
  'collections' => 
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
  'roles' => 
  array (
    0 => 'user_roles',
  ),
  'sp_permissions' => 
  array (
    0 => 'user_sp_permissions',
  ),
  'user_roles' => 
  array (
    0 => 'users',
    1 => 'roles',
  ),
  'user_sp_permissions' => 
  array (
    0 => 'users',
    1 => 'sp_permissions',
  ),
  'user_tb_permissions' => 
  array (
    0 => 'users',
  ),
  'users' => 
  array (
    0 => 'users',
    1 => 'api_keys',
    2 => 'collections',
    3 => 'files',
    4 => 'folder_other_permissions',
    5 => 'folder_permissions',
    6 => 'folders',
    7 => 'user_roles',
    8 => 'user_sp_permissions',
    9 => 'user_tb_permissions',
  ),
),
        'relation_type'  => array (
  'api_keys~users' => 'n:1',
  'collections~users' => 'n:1',
  'files~users' => 'n:1',
  'folder_other_permissions~users' => 'n:1',
  'folder_permissions~users' => 'n:1',
  'folders~users' => 'n:1',
  'roles~user_roles' => '1:n',
  'sp_permissions~user_sp_permissions' => '1:n',
  'user_roles~users' => 'n:1',
  'user_roles~roles' => 'n:1',
  'user_sp_permissions~users' => 'n:1',
  'user_sp_permissions~sp_permissions' => 'n:1',
  'user_tb_permissions~users' => 'n:1',
  'users~users' => '1:n',
  'users~api_keys' => '1:n',
  'users~collections' => '1:n',
  'users~files' => '1:n',
  'users~folder_other_permissions' => '1:n',
  'users~folder_permissions' => '1:n',
  'users~folders' => '1:n',
  'users~user_roles' => '1:n',
  'users~user_sp_permissions' => '1:n',
  'users~user_tb_permissions' => '1:n',
  'roles~users' => 'n:m',
  'users~roles' => 'n:m',
  'sp_permissions~users' => 'n:m',
  'users~sp_permissions' => 'n:m',
),
        'multiplicity'   => array (
  'api_keys~users' => false,
  'collections~users' => false,
  'files~users' => false,
  'folder_other_permissions~users' => false,
  'folder_permissions~users' => false,
  'folders~users' => false,
  'roles~user_roles' => true,
  'sp_permissions~user_sp_permissions' => true,
  'user_roles~users' => false,
  'user_roles~roles' => false,
  'user_sp_permissions~users' => false,
  'user_sp_permissions~sp_permissions' => false,
  'user_tb_permissions~users' => false,
  'users~users' => true,
  'users~api_keys' => true,
  'users~collections' => true,
  'users~files' => true,
  'users~folder_other_permissions' => true,
  'users~folder_permissions' => true,
  'users~folders' => true,
  'users~user_roles' => true,
  'users~user_sp_permissions' => true,
  'users~user_tb_permissions' => true,
  'roles~users' => true,
  'users~roles' => true,
  'sp_permissions~users' => true,
  'users~sp_permissions' => true,
),
];