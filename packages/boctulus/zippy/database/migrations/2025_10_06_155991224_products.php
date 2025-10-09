<?php

use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Migration;

class Products extends Migration
{
    protected $table      = 'products';
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
        // EAN (barcode)
        ->bigint('ean')->unsigned()->pri()
        // Product description
        ->text('description')->nullable()
        // Net content (numeric quantity, e.g. 900.00)
        ->decimal('net_content', 10, 2)->nullable()
        // Unit of measurement for net_content (e.g. "cm3", "ml", "g", "l")
        ->varchar('unit_of_measurement', 50)->nullable()
        // Brand
        ->varchar('brand', 100)->nullable()
        // Image path or URL (optional)
        ->varchar('img', 255)->nullable()
        // Categories as JSON array of simple strings, e.g. ["oil","cooking"]
        // If your DB doesn't support native JSON, consider using ->text('categories')->nullable() instead.
        ->json('categories')->nullable()
        ->varchar('catego_raw1', 100)->nullable()
        ->varchar('catego_raw2', 100)->nullable()
        ->varchar('catego_raw3', 100)->nullable()        
        // created_at / updated_at
        ->datetimes();

        $sc->create();

        // If you prefer EAN to be unique across catalog, uncomment:
        // Schema::table($this->table, function($table){
        //     $table->unique('ean');
        // });
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
