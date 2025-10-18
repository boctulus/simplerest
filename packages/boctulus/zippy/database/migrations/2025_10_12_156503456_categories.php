<?php

use Boctulus\Simplerest\Core\Libs\Migration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

class Categories extends Migration
{
    protected $table      = 'categories';
    protected $connection = 'zippy';

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
        ->varchar('id', 21)->pri()
        ->varchar('name', 150)->notNullable()
        ->varchar('slug', 150)->unique()
        ->varchar('image_url', 255)->nullable()
        ->varchar('store_id',30)->nullable()
        ->varchar('parent_id',21)->nullable()
        ->varchar('parent_slug', 150)->nullable()
        ->enum('proposed_by', ['human', 'llm', 'neural network'])->default('llm')
        ->boolean('is_approved')->default(false)
        ->boolean('is_active')->default(true)
        ->timestamps()
        ->softDeletes();

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


