<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Schema;

class Prompts implements IMigration
{
    protected $table = 'prompts';

    function __construct(){
        ### CONSTRUCTOR
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
        ->varchar('title', 100)
        ->integer('project')->nullable()  /* la idea es que los prompts pertenezcan a un proyecto */
        ->text('description')
        ->varchar('base_path', 100)
        ->json('files')
        ->text('notes')->nullable()
		// ...
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


