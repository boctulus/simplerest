<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

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
