<?php 

return [
        'relation_type'=> array (
			  'api_keys~tbl_usuario_empresa' => 'n:1',
			  'collections~tbl_usuario_empresa' => 'n:1',
			  'files~tbl_usuario_empresa' => 'n:1',
			  'folder_other_permissions~tbl_usuario_empresa' => 'n:1',
			  'folder_permissions~tbl_usuario_empresa' => 'n:1',
			  'folders~tbl_usuario_empresa' => 'n:1',
			  'roles~user_roles' => '1:n',
			  'sp_permissions~user_sp_permissions' => '1:n',
			  'tbl_usuario_empresa~folder_other_permissions' => '1:n',
			  'tbl_usuario_empresa~user_sp_permissions' => '1:n',
			  'tbl_usuario_empresa~api_keys' => '1:n',
			  'tbl_usuario_empresa~folder_permissions' => '1:n',
			  'tbl_usuario_empresa~collections' => '1:n',
			  'tbl_usuario_empresa~user_tb_permissions' => '1:n',
			  'tbl_usuario_empresa~folders' => '1:n',
			  'tbl_usuario_empresa~files' => '1:n',
			  'tbl_usuario_empresa~user_roles' => '1:n',
			  'user_roles~roles' => 'n:1',
			  'user_roles~tbl_usuario_empresa' => 'n:1',
			  'user_sp_permissions~sp_permissions' => 'n:1',
			  'user_sp_permissions~tbl_usuario_empresa' => 'n:1',
			  'user_tb_permissions~tbl_usuario_empresa' => 'n:1',
			  'roles~tbl_usuario_empresa' => 'n:m',
			  'tbl_usuario_empresa~roles' => 'n:m',
			  'sp_permissions~tbl_usuario_empresa' => 'n:m',
			  'tbl_usuario_empresa~sp_permissions' => 'n:m',
			),
        'multiplicity' => array (
			  'api_keys~tbl_usuario_empresa' => false,
			  'collections~tbl_usuario_empresa' => false,
			  'files~tbl_usuario_empresa' => false,
			  'folder_other_permissions~tbl_usuario_empresa' => false,
			  'folder_permissions~tbl_usuario_empresa' => false,
			  'folders~tbl_usuario_empresa' => false,
			  'roles~user_roles' => true,
			  'sp_permissions~user_sp_permissions' => true,
			  'tbl_usuario_empresa~folder_other_permissions' => true,
			  'tbl_usuario_empresa~user_sp_permissions' => true,
			  'tbl_usuario_empresa~api_keys' => true,
			  'tbl_usuario_empresa~folder_permissions' => true,
			  'tbl_usuario_empresa~collections' => true,
			  'tbl_usuario_empresa~user_tb_permissions' => true,
			  'tbl_usuario_empresa~folders' => true,
			  'tbl_usuario_empresa~files' => true,
			  'tbl_usuario_empresa~user_roles' => true,
			  'user_roles~roles' => false,
			  'user_roles~tbl_usuario_empresa' => false,
			  'user_sp_permissions~sp_permissions' => false,
			  'user_sp_permissions~tbl_usuario_empresa' => false,
			  'user_tb_permissions~tbl_usuario_empresa' => false,
			  'roles~tbl_usuario_empresa' => true,
			  'tbl_usuario_empresa~roles' => true,
			  'sp_permissions~tbl_usuario_empresa' => true,
			  'tbl_usuario_empresa~sp_permissions' => true,
			)
];