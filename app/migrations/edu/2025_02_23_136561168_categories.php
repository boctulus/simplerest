<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class CategoriesTable implements IMigration
{
    protected $table = 'categories';

    function __construct() {
        DB::setConnection('edu');
    }

    public function up()
    {
        $sc = new Schema($this->table);

        $sc
        ->integer('id')->auto()->pri()
        ->varchar('name', 100)->unique()
        ->datetimes();

        $sc->create();
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
