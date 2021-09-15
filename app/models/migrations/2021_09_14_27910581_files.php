<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;

class Files implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        Factory::config()['db_connection_default'] = 'main';

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

        $users_table = config()['users_table'];
        $users_pri   = get_name_id($users_table);

        $sc
        ->foreign('belongs_to')->references($users_pri)->on($users_table)->onDelete('cascade');

        $res = $sc->create();

    }
}

