<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class CourseTagTable implements IMigration
{
    protected $table = 'course_tag';

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
        ->integer('course_id')->index()
        ->integer('tag_id')->index();

        // Relaciones
        $sc->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        $sc->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');

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
