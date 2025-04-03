<?php

use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Interfaces\IMigration;

class CreatePromptsTb implements IMigration
{
    protected $table = 'prompt';

    function __construct()
    {
        DB::getConnection('main');
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


