<?php

namespace Boctulus\Simplerest\pages\admin;

class AclPermissions
{
    public $tpl_params = [
        'title'      => 'ACL Permissions',
        'page_name'  => 'ACL Permissions',
        'footer'     => '<!-- footer -->'
    ];

    function index(){
        ob_start();
        require __DIR__ . '/../../Views/admin/acl_permissions.php';
        return ob_get_clean();
    }
}
