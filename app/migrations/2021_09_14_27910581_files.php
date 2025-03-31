<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

class Files implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        get_default_connection();

        $sc = (new Schema('files'))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->varchar('uuid', 36)->pri()
        ->varchar('filename', 255)
        ->varchar('file_ext', 30)
        ->varchar('filename_as_stored', 60)
        ->integer('belongs_to')->nullable()->index()
        ->bool('guest_access')->nullable()
        ->bool('locked')->default(0)
        ->bool('broken')->nullable()
        ->datetime('created_at')
        ->datetime('deleted_at')->nullable();

        $users_table = Config::get()['users_table'];
        $users_pri   = get_id_name($users_table);

        $sc
        ->foreign('belongs_to')->references($users_pri)->on($users_table)->onDelete('cascade');

        $res = $sc->create();
    }

}

