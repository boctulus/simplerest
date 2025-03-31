<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

class CourseStudentTable implements IMigration
{
    protected $table = 'course_student';

    function __construct() {
        DB::setConnection('edu');
    }

    public function up()
    {
        $courses_table = 'courses';
        $courses_pri = 'id';

        $users_table = 'users';
        $users_pri = 'id';

        $sc = new Schema($this->table);

        $sc
        ->integer('course_id')->index()
        ->integer('user_id')->index();

        $sc->primary(['course_id', 'user_id']);

        $sc->foreign('course_id')->references($courses_pri)->on($courses_table)->onDelete('cascade');
        $sc->foreign('user_id')->references($users_pri)->on($users_table)->onDelete('cascade');

        $sc->create();
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
