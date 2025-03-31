<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Libs\System;
use Boctulus\Simplerest\Core\Controllers\ConsoleController;

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

        if (DB::table('users')->where($data)->exists()){
            return;
        }

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

        $commands = [
            'migrations fresh --to=main --force',
            'migrations migrate',
            'make model all --from:main',
            'make schema all -f --from:main',
            'make acl'
        ];

        foreach ($commands as $cmd){
            $res = System::com($cmd);
            print_r($res);
        }
        
        Schema::enableForeignKeyConstraints();

        $this->create_first_user();
    }

}

