<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;

class Folders implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        get_default_connection();;

		$sc = new Schema('folders');

        $sc
        ->integer('id')->auto()->pri()
        ->varchar('tb', 50)->index()
        ->varchar('name', 50)->index()
        ->integer('belongs_to')->index()
        ->datetime('created_at');

        $users_table = config()['users_table'];
        $users_pri   = get_name_id($users_table);

        $sc
        ->foreign('belongs_to')->references($users_pri)->on($users_table)->onDelete('cascade');

        $res = $sc->create();
    }
}

