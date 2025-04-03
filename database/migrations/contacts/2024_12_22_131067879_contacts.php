<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

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


