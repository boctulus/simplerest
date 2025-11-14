<?php

use Boctulus\Simplerest\Core\Libs\Migration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Libs\DB;

return new class extends Migration
{
    protected $table      = 'brand_categories';
    protected $connection = 'zippy';

    public function up()
    {
        $sc = new Schema($this->table);
        $sc
            ->bigint('id')->auto()->pri()
            ->bigint('brand_id')->notNullable()->index()->comment('Foreign key to brands table')
            ->varchar('category_id', 21)->notNullable()->index()->comment('Foreign key to categories table')
            ->datetimes()
            ->softDeletes();

        $sc->create();

        // Add foreign key constraint to categories table
        DB::statement("ALTER TABLE {$this->table} ADD CONSTRAINT fk_brandcat_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE");
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
};