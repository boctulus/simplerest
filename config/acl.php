<?php

use boctulus\grained_acl\Acl;

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
    ->addResourcePermissions('products', ['read', 'write'])
    ->addResourcePermissions('testx', ['read', 'write'])
    ->addResourcePermissions('facturas', ['read', 'write'])
    ->addResourcePermissions('tbl_estado_civil', ['read', 'write'])
    ->addResourcePermissions('tbl_factura_detalle', ['read', 'write'])
    // ...
    //->setAsGuest('guest')

    ->addRole('registered', 1)
    ->addInherit('guest') 
    ->addResourcePermissions('tbl_persona', ['read', 'write'])
    ->addResourcePermissions('u', ['read'])
    ->addResourcePermissions('tbl_scritp_tablas', ['read'])
    ->addResourcePermissions('products', ['read_all'])
    //->addResourcePermissions('tbl_usuario_empresa', ['read'])
    ->addResourcePermissions('tbl_estado_civil', ['read'])
    ->addResourcePermissions('tbl_categoria_persona', ['read'])
    ->addResourcePermissions('tbl_estado', ['read', 'write'])
    ->addResourcePermissions('tbl_genero', ['read', 'write'])
    ->addResourcePermissions('tbl_empresa', ['read'])
    ->addResourcePermissions('tbl_cuenta_contable', ['read'])
    ->addResourcePermissions('files', ['read', 'write'])
    ->addResourcePermissions('tbl_categoria_persona_persona', ['read'])
    ->addResourcePermissions('tbl_tipo_documento', ['read_all'])
    ->addResourcePermissions('sp_permissions', ['read'])
    ->addResourcePermissions('user_sp_permissions', ['read', 'write'])
    ->addResourcePermissions('updates', ['read_all'])  // *
    
    // ...
    ->setAsRegistered('registered')
    
    
    ->addRole('usuario', 10) 
    ->addInherit('registered')
    ->addResourcePermissions('tbl_descuento', ['read'])
    // ...

    
    ->addRole('usuario_plus', 11) 
    ->addInherit('usuario')

    ->addRole('moderador', 13) 
    ->addInherit('usuario_plus')
    

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
