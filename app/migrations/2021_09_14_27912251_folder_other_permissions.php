<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;

class FolderOtherPermissions implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Factory::config()['db_connection_default'] = 'main';

		$sc = new Schema('folder_other_permissions');

        $sc
        ->int('id')->auto()->unsigned()->pri()
        ->int('folder_id')->index()
        ->int('belongs_to')->index()
        ->bool('guest')->default(0)
        ->bool('r')
        ->bool('w')
        ->datetime('created_at');

        $users_table = config()['users_table'];
        $users_pri   = get_name_id($users_table);

        $sc
        ->foreign('belongs_to')->references($users_pri)->on($users_table)->onDelete('cascade');

        $res = $sc->create();

    }
}
