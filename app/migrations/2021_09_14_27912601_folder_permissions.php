<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Schema;

class FolderPermissions implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        get_default_connection();

		$sc = new Schema('folder_permissions');

        $sc
        ->int('id')->auto()->pri()
        ->int('folder_id')->index()
        ->int('belongs_to')->index()
        ->int('access_to')->index()
        ->bool('r')
        ->bool('w')
        ->datetime('created_at');

        $users_table = Config::get()['users_table'];
        $users_pri   = get_id_name($users_table);

        $sc
        ->foreign('belongs_to')->references($users_pri)->on($users_table)->onDelete('cascade')
        ->foreign('access_to')->references($users_pri)->on($users_table)->onDelete('cascade');

        $res = $sc->create();
    }
}

