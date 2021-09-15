<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;

class Collections implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */  
    public function up()
    {
        Factory::config()['db_connection_default'] = 'main';

		$sc = new Schema('collections');
        
        $sc
        ->integer('id')->auto()->unsigned()->pri()
        ->varchar('entity', 80)
        ->longtext('refs')
        ->integer('belongs_to')->index()
        ->datetime('created_at');

        $users_table = config()['users_table'];
        $users_pri   = get_name_id($users_table);

        $sc
        ->foreign('belongs_to')->references($users_pri)->on($users_table)->onDelete('cascade');

        $res = $sc->create();

    }
}

