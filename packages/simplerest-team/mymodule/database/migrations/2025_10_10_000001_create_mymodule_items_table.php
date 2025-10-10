<?php

use Boctulus\Simplerest\Core\DB;
use Boctulus\Simplerest\Core\Schema;
use Boctulus\Simplerest\Core\Migration;

/**
 * Migración para crear la tabla de items del módulo myModule
 */
class CreateMymoduleItemsTable extends Migration
{
    /**
     * Ejecutar la migración
     */
    public function up()
    {
        Schema::createTable('mymodule_items', function($table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });

        // Insertar datos de prueba
        DB::table('mymodule_items')->insert([
            [
                'name' => 'Item de Prueba 1',
                'description' => 'Este es un item de prueba creado por la migración',
                'active' => true,
                'price' => 99.99,
                'quantity' => 10,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Item de Prueba 2',
                'description' => 'Segundo item de prueba',
                'active' => false,
                'price' => 49.99,
                'quantity' => 5,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Item de Prueba 3',
                'description' => 'Tercer item de prueba',
                'active' => true,
                'price' => 149.99,
                'quantity' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Revertir la migración
     */
    public function down()
    {
        Schema::dropTable('mymodule_items');
    }
}
