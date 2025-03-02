<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Config;
use simplerest\core\libs\Schema;
use simplerest\core\libs\System;
use users;

class UserRoles implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        get_default_connection();;

        $sc = (new Schema('user_roles'))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer('id')->auto()->pri()
        ->integer('user_id')->index()
        ->integer('role_id')->index()
        ->integer ('created_by')->nullable()->index()
        ->datetime('created_at')
        ->integer('updated_by')->nullable()->index()
        ->datetime('updated_at')->nullable();

        /*
            Esta debe depender del nombre de la tabla users y del id de dicha tabla 
        */

        // El helper get_id_name() requiere que el schema exista 
        System::com("make schema users --from:main");

        $users_table = Config::get()['users_table'];
        $users_pri   = get_id_name($users_table);

        $sc->foreign('user_id')->references($users_pri)->on($users_table)->onDelete('cascade');
        $sc->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

        $res = $sc->create();
    }
}

