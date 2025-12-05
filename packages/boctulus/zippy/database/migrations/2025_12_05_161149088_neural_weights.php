<?php

use Boctulus\Simplerest\Core\Libs\Migration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

class NeuralWeights extends Migration
{
    protected $table      = 'neural_weights';
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
        ->integer('id')->auto()->pri()
        ->varchar('word', 100)->index()
        ->varchar('category_slug', 100)->index()
        ->decimal('weight', 4, 3)->default(0.500)->comment('Peso de la palabra para la categoría (0-1)')
        ->varchar('source', 20)->comment('Origen del peso: automatic, manual, trained, learned')
        ->integer('usage_count')->default(0)->comment('Veces que se usó esta palabra para clasificar')
        ->datetime('last_used_at')->nullable()->comment('Última vez que se usó')
        ->datetimes();

        $sc->unique(['word', 'category_slug'], 'uk_word_category');
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


