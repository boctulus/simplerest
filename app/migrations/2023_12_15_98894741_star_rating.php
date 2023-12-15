<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class StarRating implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('star_rating');

        $sc
        ->integer('id')->auto()->pri()
        ->text('comment')->nullable()
        ->int('score')
        ->varchar('author')
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
        $sc = new Schema('star_rating');
        $sc->dropTableIfExists();
    }
}

