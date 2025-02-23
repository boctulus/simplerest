<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class AddActiveToCourses implements IMigration
{
    protected $table = 'courses';

    function __construct(){
        DB::setConnection('edu');
    }

    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        ### UP

        $sc = new Schema($this->table);
        $sc->tinyint('active')->default(1)->after('title');
        $sc->alter();
    }

    /**
	* Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        ### DOWN

        $sc = new Schema($this->table);
        $sc->dropColumn('active');
        $sc->alter();
    }
}
