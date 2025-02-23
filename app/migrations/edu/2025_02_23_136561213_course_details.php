<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class CourseDetailsTable implements IMigration
{
    protected $table = 'course_details';

    function __construct() {
        DB::setConnection('edu');
    }

    public function up()
    {
        $courses_table = 'courses';
        $courses_pri = 'id';

        $sc = new Schema($this->table);

        $sc
        ->integer('id')->auto()->pri()
        ->integer('course_id')->unique()
        ->text('description')
        ->integer('duration') // En horas
        ->varchar('difficulty', 50)
        ->datetimes();

        $sc->foreign('course_id')->references($courses_pri)->on($courses_table)->onDelete('cascade');

        $sc->create();
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
