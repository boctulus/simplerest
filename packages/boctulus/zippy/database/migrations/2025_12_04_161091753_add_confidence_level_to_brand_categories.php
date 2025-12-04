<?php

use Boctulus\Simplerest\Core\Libs\Migration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

class AddConfidenceLevelToBrandCategories extends Migration
{
    protected $table      = 'brand_categories';
    protected $connection = 'zippy';

    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        DB::setConnection($this->connection);

        // Agregar columna confidence_level
        DB::statement("ALTER TABLE {$this->table} ADD COLUMN confidence_level ENUM('high', 'medium', 'low', 'doubtful') DEFAULT 'medium' AFTER category_id");
    }

    /**
	* Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        DB::setConnection($this->connection);

        // Eliminar columna confidence_level
        DB::statement("ALTER TABLE {$this->table} DROP COLUMN confidence_level");
    }
}


