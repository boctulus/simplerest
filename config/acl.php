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
    ->addRole('guest', -1)
    ->addResourcePermissions('baz', ['read'])
    ->addResourcePermissions('bar', ['read', 'write'])

    //->setGuest('guest')

    ->addRole('registered', 1)
    ->addInherit('guest')
    ->addResourcePermissions('user_roles', ['read'])
    ->addResourcePermissions('super_cool_table', ['read', 'write'])
    
    
    ->addRole('usuario', 10) 
    ->addInherit('registered')
    ->addSpecialPermissions(['read_all'])
    

    ->addRole('admin', 50) 
    ->addInherit('registered')
    //->addResourcePermissions('tbl_empresa', ['read', 'write'])
    ->addSpecialPermissions(['read_all'])
    
   
    ->addRole('supervisor', 100)  // salta sino especifico el id al leerlo
    ->addInherit('registered')
    ->addResourcePermissions('users', ['read_all'])  // <--

 
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
        'read_all_trashcan',
        'write_all_trashcan',
        'write_all_folders', 
        'write_all_collections',
        'transfer',
        'grant'
    ]);

                            

        
        

    /////////////////////

    //dd($acl->hasResourcePermission('list', ['basic'], 'super_cool_table'));
    //dd($acl->getSpPermissions());
    //dd($acl->getRolePermissions(), 'perms');
    //exit;    
    /////////////////////

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