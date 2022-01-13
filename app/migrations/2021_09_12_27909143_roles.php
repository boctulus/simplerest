<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;

class Roles implements IMigration
{
    function __construct(){
        get_default_connection();
    }
    
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = (new Schema('roles'))

        ->setEngine('InnoDB')
        ->setCharset('utf8')
        ->setCollation('utf8_general_ci')

        ->integer('id')->auto()->pri()
        ->varchar('name', 50);

        $res = $sc->create();
    }
}

