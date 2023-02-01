<?php

namespace simplerest\controllers;

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Schema;
use simplerest\core\libs\System;
use simplerest\core\controllers\ConsoleController;

class InstallController extends ConsoleController
{
    function index(){
        $this->install();
    }

    public function create_first_user(){
        $data = [
            "username" => "adm1",
            "email" => "adm1@mail.com",
            "password" => "gogogo",
            "is_active" => 1
        ];

        DB::transaction(function() use($data) {
            $uid = DB::table('users')->insert($data);
    
            table('user_roles')->insert([
                "user_id" => $uid,
                "role_id" => 10000  // deberia usando el Acl() entregar el role_id del rol mas alto en la jerarquia
            ]);
        });

        // el metodo DB::transaction() deberia devolver si ha fallado o no 
        dd($data, 'USER CREATED');
    }

    /*
        Faltaría copiar el .env-example a .env
        y correr el composer install
    */
    private function install()
    {    
        Schema::disableForeignKeyConstraints();
        
        $res = System::com('migrations migrate');
        print_r($res);
        
        Schema::enableForeignKeyConstraints();

        $res = System::com("make model all --from:main");
        print_r($res);

        $res = System::com("make schema all -f --from:main");
        print_r($res);

        $res = System::com("make acl");
        print_r($res);

        $this->create_first_user();
    }

}

