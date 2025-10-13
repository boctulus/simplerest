<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\API\v1\ResourceController;
use Boctulus\Simplerest\Core\Acl;

class DumbAuthController extends ResourceController
{
    function __construct()
    {
        global $api_version;
        $api_version = 'v1';

        parent::__construct();

        // aquí ******  

        if (!$this->acl->hasAnyRoleOrHigher(['supervisor', 'admin'])){
            response()->error('Unauthorized ***', 401);
        }

    }

    function index(){
        return 'Hello World';
    }

    function super_cool_action(int $a = 0)
    {
        $acl = $this->acl;


        //dd($acl-> getEveryPossibleSpPermissions(), 'EVERY POSSIBLE SP PERM');
        
        //dd($acl->getTbPermissions(), 'TB PERMs');
        //dd($acl->getFreshTbPermissions(), 'FRESH TB PERMs');

        //dd($acl->getSpPermissions(), 'SP PERMs');
        //dd($acl->getFreshSpPermissions(), 'FRESH SP PERMs');

        dd($acl->getRoles(), 'ROLES');

        dd($acl->getAncestry('dsi'), "ANCESTER FOR DSI");


        //dd($acl->isHigherRole('admin', 'registered'));
        //dd($acl->isHigherRole('registered', 'admin'));
        //dd($acl->isHigherRole('registered', 'registered'));
        // dd($acl->isHigherRole('dsi', 'supervisor'));
        // dd($acl->isHigherRole('supervisor', 'dsi'));
        //dd($acl->isHigherRole('supervisor', 'usuario'));
        
        //dd($acl->hasRoleOrHigher('admin'));

        dd($acl->roleExists('cajero'), "ROLE CAJERO EXISTS?");
        dd($acl->roleExists('admin'), "ROLE ADMIN EXISTS?");

        dd($acl->getEveryPossibleRole(), "ANY POSSIBLE ROLE");
        dd($acl->getRolePermissions('registered'), "ROLE PERMISSIONS FOR REGISTERED");


        // toca hacer que isHigherRole() se arme con permisos y no roles
        dd($acl->hasAnyRoleOrHigher(['admin', 'dsi']), "HAS ANY ROLE OR HIGHER ONE?");

        dd(auth()->isRegistered(), 'IS GUEST'); 
        dd($acl->isRegistered(), 'IS REGISTERED');
        
        
        dd($acl->hasRole('cajero'), 'HAS ROL CAJERO');
        dd($acl->hasRole('dsi'), 'HAS ROL DSI');
        
        dd($acl->hasResourcePermission('show', 'tbl_factura'), 'READ PERM FOR TBL_FACTURA?');
        dd($acl->hasSpecialPermission('read_all'), 'READ_ALL PERM?');

        exit; ///

        // acción cualquiera: 
        // por ejemplo incrementar un valor
        return ++$a;
    }     


}