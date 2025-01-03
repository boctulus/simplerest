<?php

use boctulus\grained_acl\Acl;
use simplerest\core\libs\Files;


// debería leerse de archivo
$acl_cache = false;
$acl_file  = config()['acl_file'];

// Check whether ACL data already exist
if (!$acl_cache || is_file($acl_file) !== true) {

    /*
        Roles are backed in database but role permissions not.
        Role permissions can be decorated and these decorators are backed.
    */

    $acl = new Acl();

    $acl
    ->addRole('guest', -1)
    // ...
    ->addResourcePermissions('prompts', ['read', 'write'])
    //->setAsGuest('guest')

    ->addRole('registered', 1)
    ->setAsRegistered('registered')
    ->addInherit('guest') 
    // ->addResourcePermissions('bar', ['read', 'write'])
    // ->addResourcePermissions('products', ['read'])
    // ->addResourcePermissions('tbl_persona', ['read', 'write'])
    // ->addResourcePermissions('u', ['read'])
    // ->addResourcePermissions('tbl_scritp_tablas', ['read'])
    // ->addResourcePermissions('products', ['read_all'])
    // ->addSpecialPermissions([
    //     'read_all', 
    //     'write_all',
    //     'write_all_collections'
    // ])
    //->addResourcePermissions('tbl_usuario_empresa', ['read'])
      

    ->addRole('admin', 50) 
    ->addInherit('registered')
    //->addSpecialPermissions(['read_all', 'write_all'])
    ->addResourcePermissions('tbl_contacto', ['read'])
    ->addResourcePermissions('tbl_usuario_empresa', ['read', 'write'])
    ->addResourcePermissions('tbl_factura', ['read', 'write'])    


    ->addRole('update_subscriber', 75) 
    ->addInherit('registered')
    ->addResourcePermissions('webhooks', ['read'])  

   
    ->addRole('supervisor', 100)  
    ->addInherit('registered')
    ->addResourcePermissions('tbl_usuario_empresa', ['read_all']) 
    ->addResourcePermissions('tbl_factura', ['read', 'write'])
    ->addResourcePermissions('webhooks', ['read', 'write'])
    // ->addSpecialPermissions([
    //     'fill_all', 
    // ])

 
    ->addRole('dsi', 500)
    ->addInherit('supervisor')
    ->addSpecialPermissions([
        'read_all', 
        'write_all', 
        'read_all_folders', 
        'lock', 
        'fill_all', 
        'impersonate',        
        'read_all_trashcan',
        'write_all_trashcan',
        'write_all_folders', 
        'write_all_collections'
    ])     

    ->addRole('superadmin', 900)
    ->addInherit('dsi')
    ->addSpecialPermissions([
        'transfer',
        'grant'
    ]);

    if (!is_dir(SECURITY_PATH)){
        Files::mkDirOrFail(SECURITY_PATH);
    }

    // Store serialized list into plain file
    $bytes = file_put_contents(
        $acl_file,
        serialize($acl)
    );

    if ($bytes === 0){
        throw new \Exception("Internal Error. ACL File could not be written");
    }
} else {
    // Restore ACL object from serialized file

    $acl = unserialize(
        file_get_contents($acl_file)
    );
}


//var_export($acl->getRolePermissions());

return $acl;
