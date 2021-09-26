<?php

use boctulus\grained_acl\Acl;
use simplerest\libs\Debug;


$acl_cache = false;
$acl_file = '../app/security/acl.cache';

// Check whether ACL data already exist
if (!$acl_cache || is_file($acl_file) !== true) {

    /*
        Roles are backed in database but role permissions not.
        Role permissions can be decorated and these decorators are backed.
    */

    $acl = new Acl();

    $acl
    ->addRole('guest', 1)
    // ...
    //->setAsGuest('guest')

    ->addRole('registered', 2)
    ->addInherit('guest')
    ->addResourcePermissions('tbl_scritp_tablas', ['read'])
    ->addResourcePermissions('products', ['read_all'])
    //->addResourcePermissions('tbl_usuario_empresa', ['read'])
    ->addResourcePermissions('tbl_estado_civil', ['read'])
    ->addResourcePermissions('tbl_categoria_persona', ['read'])
    ->addResourcePermissions('tbl_estado', ['read', 'write'])
    ->addResourcePermissions('tbl_genero', ['read', 'write'])
    // ...
    ->setAsRegistered('registered')
    
    
    ->addRole('usuario', 10) 
    ->addInherit('registered')
    ->addResourcePermissions('tbl_descuento', ['read'])
    // ...
    

    ->addRole('admin', 50) 
    ->addInherit('registered')
    //->addSpecialPermissions(['read_all', 'write_all'])
    ->addResourcePermissions('tbl_contacto', ['read'])
    
   
    ->addRole('supervisor', 100)  
    ->addInherit('registered')
    ->addResourcePermissions('tbl_usuario_empresa', ['read_all']) 

 
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


    if (!is_writable($acl_file)){
        throw new \Exception("$acl_file is not writable. Check permissions");   
    }

    // Store serialized list into plain file
    file_put_contents(
        $acl_file,
        serialize($acl)
    );
} else {
    // Restore ACL object from serialized file

    $acl = unserialize(
        file_get_contents($acl_file)
    );
}


//var_export($acl->getRolePermissions());

return $acl;
