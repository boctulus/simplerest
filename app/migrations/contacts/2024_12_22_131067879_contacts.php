<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class Contacts implements IMigration
{
    protected $table = 'contacts';

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
        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')
        ->integer('id')->auto()->pri()
        ->varchar('full_name', 150)
        ->varchar('company', 100)->nullable()
        ->varchar('website', 200)->nullable()
        ->varchar('job_title', 100)->nullable()        
        ->varchar('phone_number_1', 20)->nullable()
        ->varchar('phone_number_2', 20)->nullable()
        ->text('notes')->nullable()
        ->boolean('favorite')->default(false)
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


