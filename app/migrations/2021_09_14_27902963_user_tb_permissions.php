<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;

class UserTbPermissionsCreation implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        get_default_connection();

        $sc = (new Schema('user_tb_permissions'))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer('id')->auto()->pri()
        ->varchar('tb', 80)->index()
        ->bool('can_list_all')->nullable()
        ->bool('can_show_all')->nullable()
        ->bool('can_list')->nullable()
        ->bool('can_show')->nullable()
        ->bool('can_create')->nullable()
        ->bool('can_update')->nullable()
        ->bool('can_delete')->nullable()
        ->integer('user_id')->index()
        ->integer ('created_by')->nullable()->index()
        ->datetime('created_at')
        ->integer('updated_by')->nullable()->index()
        ->datetime('updated_at')->nullable();

        /*
            Esta debe depender del nombre de la tabla users y del id de dicha tabla 
        */

        $users_table = config()['users_table'];
        $users_pri   = get_id_name($users_table);

        $sc
        ->foreign('user_id')->references($users_pri)->on($users_table)->onDelete('cascade');

        $res = $sc->create();
    }
}

