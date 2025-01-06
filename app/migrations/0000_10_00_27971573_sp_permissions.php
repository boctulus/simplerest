<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class SpPermissions implements IMigration
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
        $sc = new Schema('sp_permissions');

        $sc
        ->integer('id')->auto()->pri()
        ->varchar('name', 45);

		$sc->create();

        // Insertar los permisos especiales predefinidos
        DB::table('sp_permissions')->insert([
            ['name' => 'read_all'],
            ['name' => 'read_all_folders'],
            ['name' => 'read_all_trashcan'],
            ['name' => 'write_all'],
            ['name' => 'write_all_folders'],
            ['name' => 'write_all_trashcan'],
            ['name' => 'write_all_collections'],
            ['name' => 'fill_all'],
            ['name' => 'grant'],
            ['name' => 'impersonate'],
            ['name' => 'lock'],
            ['name' => 'transfer']
        ]); 
    }

    public function down(){
        Schema::dropIfExists('sp_permissions');
    }
}

