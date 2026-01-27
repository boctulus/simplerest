<?php

use Boctulus\Simplerest\Core\Libs\Migration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

class AddPathToMigrations extends Migration
{
    protected $table      = 'migrations';
    protected $connection = null;

    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        // Check if column already exists
        if (Schema::hasColumn('migrations', 'path')) {
            return; // Column already exists, skip
        }

        $driver = DB::driver();

        switch ($driver){
            case 'sqlite':
                // SQLite no soporta ALTER TABLE ... ADD COLUMN ... AFTER
                // Primero necesitamos renombrar la tabla, recrearla y copiar los datos
                DB::statement("ALTER TABLE migrations RENAME TO migrations_old");

                DB::statement("
                CREATE TABLE migrations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    db varchar(50) DEFAULT NULL,
                    path varchar(255) DEFAULT NULL,
                    filename varchar(255) NOT NULL,
                    created_at DATETIME NULL
                )");

                DB::statement("INSERT INTO migrations (id, db, filename, created_at) SELECT id, db, filename, created_at FROM migrations_old");
                DB::statement("DROP TABLE migrations_old");
                break;

            case 'mysql':
                DB::statement("ALTER TABLE `migrations` ADD COLUMN `path` varchar(255) DEFAULT NULL AFTER `db`");
                break;

            default:
                throw new \InvalidArgumentException("Driver $driver is not fully supported for migrations");
        }
    }

    /**
	* Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        // Check if column exists before trying to drop it
        if (!Schema::hasColumn('migrations', 'path')) {
            return; // Column doesn't exist, skip
        }

        $driver = DB::driver();

        switch ($driver){
            case 'sqlite':
                // SQLite no soporta DROP COLUMN directamente
                DB::statement("ALTER TABLE migrations RENAME TO migrations_old");

                DB::statement("
                CREATE TABLE migrations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    db varchar(50) DEFAULT NULL,
                    filename varchar(255) NOT NULL,
                    created_at DATETIME NULL
                )");

                DB::statement("INSERT INTO migrations (id, db, filename, created_at) SELECT id, db, filename, created_at FROM migrations_old");
                DB::statement("DROP TABLE migrations_old");
                break;

            case 'mysql':
                DB::statement("ALTER TABLE `migrations` DROP COLUMN `path`");
                break;

            default:
                throw new \InvalidArgumentException("Driver $driver is not fully supported for migrations");
        }
    }
}


