<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

class CommonNames implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('common_names');

        $sc
        ->integer('id')->auto()->pri()
        ->varchar('gender')->nullable()
        ->varchar('text')->unique()->nullable()
        ->varchar('language', 20)->nullable()
        ->varchar('country', 20)->nullable()
        ->datetime('created_at');

		$sc->create();		
    }

    /**
	* Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        $sc = new Schema('common_names');
        $sc->dropTableIfExists();
    }
}

