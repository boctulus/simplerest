<?php

use Boctulus\Simplerest\Core\Libs\Migration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Libs\DB;

return new class extends Migration
{
    protected $table      = 'brands';
    protected $connection = 'zippy';

    public function up()
    {
        $sc = new Schema($this->table);
        $sc
            ->bigint('id')->auto()->pri()
            ->varchar('brand', 255)->notNullable()->comment('Brand name')
            ->varchar('normalized_brand', 255)->notNullable()->index()->comment('Normalized brand for matching (lower, no accents)')
            ->datetimes()
            ->softDeletes();

        $sc->create();
    }

    public function down()
    {
        Schema::dropIfExists($this->table);
    }
};