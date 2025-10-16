<?php

use Boctulus\Simplerest\Core\Libs\Migration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Libs\DB;

return new class extends Migration
{
    protected $table      = 'category_mappings';
    protected $connection = 'zippy';

    public function up()
    {
        $sc = new Schema($this->table);
        $sc
            ->bigint('id')->auto()->pri()
            ->varchar('raw_value', 255)->notNullable()->comment('Original category text from scraper')
            ->varchar('normalized', 255)->notNullable()->index()->comment('Normalized for matching (lower, no accents)')
            ->varchar('category_id', 21)->nullable()->index()->comment('FK-like to categories.id (varchar(21))')
            ->varchar('category_slug', 150)->nullable()->index()->comment('Denormalized slug for quick mapping')
            ->varchar('source', 100)->nullable()->comment('Optional origin: "mercado", "dia", "prov_x"')
            ->datetimes()
            ->softDeletes();

        $sc->create();

        DB::statement("ALTER TABLE {$this->table} ADD CONSTRAINT fk_catmap_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL");
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
};