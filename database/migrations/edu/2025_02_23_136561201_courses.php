<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

class CoursesTable implements IMigration
{
    protected $table = 'courses';

    function __construct() {
        DB::setConnection('edu');
    }

    public function up()
    {
        $categories_table = 'categories';
        $categories_pri = 'id';

        $users_table = 'users';
        $users_pri = 'id';

        $sc = new Schema($this->table);

        $sc
        ->integer('id')->auto()->pri()
        ->varchar('title', 150)
        ->integer('category_id')->index()
        ->integer('professor_id')->index()
        ->datetimes();

        $sc->foreign('category_id')->references($categories_pri)->on($categories_table)->onDelete('cascade');
        $sc->foreign('professor_id')->references($users_pri)->on($users_table)->onDelete('cascade');

        $sc->create();
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}

