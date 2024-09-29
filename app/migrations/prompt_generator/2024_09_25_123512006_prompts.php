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
        ->varchar('title', 100)->nullable()
        ->integer('project')->nullable()  
        ->text('description')
        ->varchar('base_path', 100)->nullable()
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


