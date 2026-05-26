<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Schema;

use Boctulus\Simplerest\Core\Libs\DBRels;

class UserDenyPermissionsCreation implements IMigration
{
    /**
     * Run migration.
     *
     * @return void
     */
    public function up()
    {
        get_default_connection();

        $sc = (new Schema('user_deny_permissions'))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer('id')->auto()->pri()
        ->integer('user_id')->index()
        ->varchar('resource', 100)
        ->varchar('action', 50)
        ->integer('created_by')->nullable()->index()
        ->datetime('created_at')->nullable()
        ->integer('updated_by')->nullable()->index()
        ->datetime('updated_at')->nullable();

        $sc->unique(['user_id', 'resource', 'action'], 'uq_user_res_action');

        $users_table = Config::get()['users_table'];
        $users_pri   = get_id_name($users_table);

        $sc->foreign('user_id')->references($users_pri)->on($users_table)->onDelete('cascade');

        $sc->create();
    }

    public function down()
    {
        $sc = new Schema('user_deny_permissions');
        $sc->drop();
    }
}
