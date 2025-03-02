<?php

use boctulus\grained_acl\Acl;
use simplerest\core\libs\Config;
use simplerest\libs\Debug;



$acl_cache = false;
$acl_file  = Config::get()['acl_file'];

// Check whether ACL data already exist
if (!$acl_cache || is_file($acl_file) !== true) {

    /*
        Roles are backed in database but role permissions not.
        Role permissions can be decorated and these decorators are backed.
    */

    $acl = new Acl();

    $acl
    ->addRole('guest', 1) 
    ->addSpecialPermissions([
        'read_all',
        'write_all',
    ])
    ->setAsGuest('guest')

    ->addRole('registered', 2)
    // ->addInherit('guest')
   
    ->addRole('supervisor', 500)  
    ->addInherit('registered')
    ->addResourcePermissions('users', ['read_all'])  // <--

    ->addRole('admin', 1000)
    ->addInherit('registered')
    ->addSpecialPermissions(['read_all', 'write_all', 'read_all_folders', 'lock', 'fill_all', 'impersonate'])
 

    ->addRole('superadmin', 5000)
    ->addInherit('admin')
    ->addSpecialPermissions([
        'read_all_trashcan',
        'write_all_trashcan',
        'write_all_folders', 
        'write_all_collections',
        'transfer',
        'grant'
    ]);


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
