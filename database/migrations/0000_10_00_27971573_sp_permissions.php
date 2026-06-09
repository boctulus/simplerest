<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Libs\DB;

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
        ->varchar('name', 45)->unique();

		$sc->create();

        // Insertar los permisos especiales (capabilities) predefinidos
        $permissions = [
            'read_all', 'read_all_folders', 'read_all_trashcan',
            'write_all', 'write_all_folders', 'write_all_trashcan',
            'write_all_collections', 'fill_all',
            'grant', 'impersonate', 'lock', 'transfer',
        ];

        foreach ($permissions as $name) {
            DB::query(
                "INSERT IGNORE INTO sp_permissions (name) VALUES (?)",
                [$name]
            );
        }
    }

    public function down(){
        Schema::dropIfExists('sp_permissions');
    }
}

