<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;

class UserSpPermissions implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        get_default_connection();

        $sc = (new Schema('user_sp_permissions'))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer('id')->auto()->pri()
        ->integer('sp_permission_id')->index()
        ->integer('user_id')->index()
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
        $sc->foreign('sp_permission_id')->references('id')->on('sp_permissions')->onDelete('cascade');

        $res = $sc->create();
    }
}

