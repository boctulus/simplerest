<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class Products implements IMigration
{
    protected $table = 'products';

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

        $sc->integer('id')->auto()->pri()
        ->varchar('name', 80)
        ->varchar('type', 20)->nullable()
        ->varchar('regular_price')->nullable()
        ->varchar('sale_price')->nullable()
        ->text('description')
        ->varchar('short_description', 512)->nullable()
        ->varchar('slug', 100)->unique()
        ->text('images')
        ->varchar('categories', 250)->nullable()
        ->varchar('tags', 250)->nullable()
        ->text('dimensions')->nullable()
        ->text('attributes')->nullable()
        ->varchar('sku', 50)->nullable()
        ->varchar('status', 20)->nullable()
        ->integer('stock')->nullable()
        ->varchar('stock_status', 30)->nullable()
        ->varchar('url_ori', 300)->nullable()->unique()
        ->integer('posted')->nullable()
        ->varchar('comment', 200)->nullable()
        ->datetime('created_at')
        ->datetime('updated_at')->nullable()
        ->decimal('cost', 10, 2)->nullable()
        ->varchar('size', 20)->nullable()
        ->integer('belongs_to')->nullable()
        ->integer('active')->nullable()
        ->integer('locked')->nullable()
        ->varchar('workspace', 50)->nullable()
        ->timestamp('deleted_at')->nullable()
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


