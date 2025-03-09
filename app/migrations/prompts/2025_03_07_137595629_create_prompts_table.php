<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class CreatePromptsTb implements IMigration
{
    protected $table = 'prompt';

    function __construct()
    {
        DB::getConnection('main-2');
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

        $sc->int('id')->pri()->auto();
        $sc->varchar('name');
        $sc->enum('project_type', ['web', 'mobile_android', 'mobile_ios', 'console', 'desktop']);
        $sc->text('body');
        $sc->datetime('created_at');
        $sc->datetime('updated_at');
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


