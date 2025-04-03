<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

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
        ->varchar('author', 100)
        ->varchar('gender', 1)->nullable()
        ->datetime('deleted_at')
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

