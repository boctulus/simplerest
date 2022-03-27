<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class Ssl implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('ssl');

        $sc
        ->integer('id')->auto()->pri()
        ->varchar('domain', 255)->unique()
        ->datetime('created_at')
        ->datetime('expires_at')->index()
        ->datetime('updated_at')->nullable()

		->create();		
    }
}

