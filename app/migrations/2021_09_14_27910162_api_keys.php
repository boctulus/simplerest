<?php

use simplerest\core\interfaces\IMigration;
use simplerest\libs\Factory;
use simplerest\libs\Schema;

class ApiKeys implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        get_default_connection();

        $sc = (new Schema('api_keys'))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->varchar('uuid', 36)->pri()
        ->varchar('value', 60)->comment('hashed')
        ->integer('user_id')->index()
        ->datetime('created_at')->nullable();

        $users_table = config()['users_table'];
        $users_pri   = get_name_id($users_table);

        $sc
        ->foreign('user_id')->references($users_pri)->on($users_table)->onDelete('cascade');

        $res = $sc->create();
    }
}

