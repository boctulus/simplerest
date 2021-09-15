<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;

class UserRoles implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Factory::config()['db_connection_default'] = 'main';

        $sc = (new Schema('user_roles'))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer('id')->auto()->unsigned()->pri()
        ->integer('user_id')->index()
        ->integer('role_id')->index()
        ->integer ('created_by')->nullable()->index()
        ->datetime('created_at')
        ->integer('updated_by')->nullable()->index()
        ->datetime('updated_at')->nullable();

        /*
            Esta debe depender del nombre de la tabla users y del id de dicha tabla 
        */

        $users_table = config()['users_table'];
        $users_pri   = get_name_id($users_table);

        $sc->foreign('user_id')->references($users_pri)->on($users_table)->onDelete('cascade');
        //$sc->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

        $res = $sc->create();
    }
}

