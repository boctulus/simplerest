<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class TagsTable implements IMigration
{
    protected $table = 'tags';

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

        $sc
        ->integer('id')->auto()->pri()
        ->varchar('name', 100)->unique()
        ->datetimes();

        $sc->create();
    }

    /**
    * Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        ### DOWN

        Schema::dropIfExists($this->table);
    }
}
