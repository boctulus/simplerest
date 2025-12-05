<?php

namespace Boctulus\Simplerest\tests;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
  return;
}

require_once __DIR__ . '/../app.php';

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Libs\DB;

/*
 * Test del ORM Layer - Static Methods & Hydration
 */

/*
    * Requiere PHPUnit y una configuración adecuada de la base de datos.
    *
    * Ejecuta con: ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/SimpleORMTest.php   
    *
*/

class SimpleORMTest extends TestCase
{
    protected static $tablesCreated = false;

    /**
     * Setup - Crear tablas de prueba una sola vez para todos los tests
     */
    public static function setUpBeforeClass(): void
    {
        if (!self::$tablesCreated) {
            // Crear tabla test_orm_authors
            try {
                $schema = new Schema('test_orm_authors');
                $schema
                    ->integer('id')->auto()->pri()
                    ->varchar('name', 100)
                    ->varchar('email', 100)->nullable()
                    ->datetimes()
                    ->softDeletes();
                $schema->create();
            } catch (\Exception $e) {
                // Tabla ya existe
            }

            // Crear tabla test_orm_books
            try {
                $schema = new Schema('test_orm_books');
                $schema
                    ->integer('id')->auto()->pri()
                    ->varchar('title', 200)
                    ->integer('author_id')->index()
                    ->decimal('price', 10, 2)->nullable()
                    ->datetimes()
                    ->softDeletes();
                $schema->create();
            } catch (\Exception $e) {
                // Tabla ya existe
            }

            self::$tablesCreated = true;
        }
    }

    /**
     * Limpiar datos antes de cada test
     */
    protected function setUp(): void
    {
        DB::statement("DELETE FROM test_orm_books");
        DB::statement("DELETE FROM test_orm_authors");
    }

    /**
     * Test: Model::hydratate() debe crear instancia con datos y marcarla como existente
     */
    public function test_hydratate_creates_instance_with_data()
    {
        $instance = new TestORMAuthor(true);
        $data = ['id' => 1, 'name' => 'Test Author', 'email' => 'test@example.com'];

        $hydrated = TestORMAuthor::hydratate($instance, $data);

        $this->assertInstanceOf(TestORMAuthor::class, $hydrated);
        $this->assertEquals('Test Author', $hydrated->name);
        $this->assertEquals('test@example.com', $hydrated->email);
        $this->assertTrue($hydrated->exists());
    }

    /**
     * Test: Model::hydratate() debe almacenar datos originales
     */
    public function test_hydratate_stores_original_data()
    {
        $instance = new TestORMAuthor(true);
        $data = ['id' => 1, 'name' => 'Original Name'];

        $hydrated = TestORMAuthor::hydratate($instance, $data);

        // Modificar el nombre
        $hydrated->name = 'Modified Name';

        // Los datos originales deben mantenerse
        $this->assertEquals('Modified Name', $hydrated->name);
        // No podemos acceder a $original directamente, pero exists() debería ser true
        $this->assertTrue($hydrated->exists());
    }

    /**
     * Test: Model::all() debe retornar todos los registros
     */
    public function test_all_returns_all_records()
    {
        // Insertar datos de prueba
        $this->insertTestAuthors(3);

        $authors = TestORMAuthor::all();

        $this->assertIsArray($authors);
        $this->assertCount(3, $authors);
        $this->assertArrayHasKey('name', $authors[0]);
    }

    /**
     * Test: Model::all() debe retornar array vacío si no hay registros
     */
    public function test_all_returns_empty_array_when_no_records()
    {
        $authors = TestORMAuthor::all();

        $this->assertIsArray($authors);
        $this->assertEmpty($authors);
    }

    /**
     * Test: Model::query() debe retornar una instancia del modelo
     */
    public function test_query_returns_model_instance()
    {
        $query = TestORMAuthor::query();

        $this->assertInstanceOf(TestORMAuthor::class, $query);
    }

    /**
     * Test: Model::query() debe permitir encadenar métodos
     */
    public function test_query_allows_method_chaining()
    {
        $this->insertTestAuthors(5);

        $authors = TestORMAuthor::query()
            ->limit(3)
            ->get();

        $this->assertIsArray($authors);
        $this->assertCount(3, $authors);
    }

    /**
     * Test: Model::query() con where() debe filtrar resultados
     */
    public function test_query_with_where_filters_results()
    {
        $this->insertTestAuthors(5);

        $authors = TestORMAuthor::query()
            ->where(['name', 'Author 1'])
            ->get();

        $this->assertCount(1, $authors);
        $this->assertEquals('Author 1', $authors[0]['name']);
    }

    /**
     * Test: Model::findOrFail() debe retornar instancia hidratada
     */
    public function test_findOrFail_returns_hydrated_instance()
    {
        $ids = $this->insertTestAuthors(1);
        $id = $ids[0];

        $author = TestORMAuthor::findOrFail($id);

        $this->assertInstanceOf(TestORMAuthor::class, $author);
        $this->assertEquals($id, $author->id);
        $this->assertEquals('Author 1', $author->name);
        $this->assertTrue($author->exists());
    }

    /**
     * Test: Model::findOrFail() debe lanzar excepción si no existe
     */
    public function test_findOrFail_throws_exception_when_not_found()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("doesn't exist");

        TestORMAuthor::findOrFail(99999);
    }

    /**
     * Test: Magic method __get() debe retornar valores de atributos
     */
    public function test_magic_get_returns_attribute_values()
    {
        $instance = new TestORMAuthor(true);
        $data = ['id' => 1, 'name' => 'Test', 'email' => 'test@test.com'];
        $hydrated = TestORMAuthor::hydratate($instance, $data);

        $this->assertEquals('Test', $hydrated->name);
        $this->assertEquals('test@test.com', $hydrated->email);
        $this->assertEquals(1, $hydrated->id);
    }

    /**
     * Test: Magic method __get() debe retornar null para atributos inexistentes
     */
    public function test_magic_get_returns_null_for_nonexistent_attributes()
    {
        $instance = new TestORMAuthor(true);
        $data = ['id' => 1, 'name' => 'Test'];
        $hydrated = TestORMAuthor::hydratate($instance, $data);

        $this->assertNull($hydrated->nonexistent_field);
    }

    /**
     * Test: Magic method __set() debe establecer valores de atributos
     */
    public function test_magic_set_sets_attribute_values()
    {
        $instance = new TestORMAuthor(true);
        $data = ['id' => 1, 'name' => 'Original'];
        $hydrated = TestORMAuthor::hydratate($instance, $data);

        $hydrated->name = 'Modified';
        $hydrated->email = 'new@test.com';

        $this->assertEquals('Modified', $hydrated->name);
        $this->assertEquals('new@test.com', $hydrated->email);
    }

    /**
     * Test: Magic method __isset() debe detectar atributos existentes
     */
    public function test_magic_isset_detects_existing_attributes()
    {
        $instance = new TestORMAuthor(true);
        $data = ['id' => 1, 'name' => 'Test', 'email' => 'test@test.com'];
        $hydrated = TestORMAuthor::hydratate($instance, $data);

        $this->assertTrue(isset($hydrated->name));
        $this->assertTrue(isset($hydrated->email));
        $this->assertTrue(isset($hydrated->id));
    }

    /**
     * Test: Magic method __isset() debe retornar false para atributos inexistentes
     */
    public function test_magic_isset_returns_false_for_nonexistent_attributes()
    {
        $instance = new TestORMAuthor(true);
        $data = ['id' => 1, 'name' => 'Test'];
        $hydrated = TestORMAuthor::hydratate($instance, $data);

        $this->assertFalse(isset($hydrated->nonexistent_field));
    }

    /**
     * Test: exists() debe retornar true para instancias hidratadas
     */
    public function test_exists_returns_true_for_hydrated_instances()
    {
        $instance = new TestORMAuthor(true);
        $data = ['id' => 1, 'name' => 'Test'];
        $hydrated = TestORMAuthor::hydratate($instance, $data);

        $this->assertTrue($hydrated->exists());
    }

    /**
     * Test: exists() debe retornar false para instancias nuevas
     */
    public function test_exists_returns_false_for_new_instances()
    {
        $instance = new TestORMAuthor(true);

        $this->assertFalse($instance->exists());
    }

    /**
     * Test: Métodos estáticos deben lanzar excepción si $table no está definida
     */
    public function test_static_methods_throw_exception_without_table_property()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage("requires static::\$table");

        // Model base no tiene $table definida
        Model::query(); 
    }

    /**
     * Test: Model::all() debe respetar soft deletes
     */
    public function test_all_respects_soft_deletes()
    {
        $ids = $this->insertTestAuthors(3);

        // Soft delete del primer autor
        $author = new TestORMAuthor(true);
        $author->where(['id', $ids[0]])->delete();

        $authors = TestORMAuthor::all();

        // Solo debe retornar 2 (el eliminado no debe aparecer)
        $this->assertCount(2, $authors);
    }

    /**
     * Test: Model::query() debe permitir filtrar por múltiples condiciones
     */
    public function test_query_with_multiple_where_conditions()
    {
        $ids = $this->insertTestAuthors(5);

        $authors = TestORMAuthor::query()
            ->where([
                ['id', $ids[0], '>'],
                ['id', $ids[4], '<=']
            ])
            ->get();

        // Debe retornar IDs del 2 al 5 (4 autores)
        $this->assertCount(4, $authors);
    }

    /**
     * Test: Model::findOrFail() debe funcionar con diferentes tipos de ID
     */
    public function test_findOrFail_works_with_different_id_types()
    {
        $ids = $this->insertTestAuthors(1);
        $id = $ids[0];

        // Probar con int
        $author1 = TestORMAuthor::findOrFail($id);
        $this->assertEquals($id, $author1->id);

        // Probar con string
        $author2 = TestORMAuthor::findOrFail((string)$id);
        $this->assertEquals($id, $author2->id);
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Helper: Insertar autores de prueba
     */
    protected function insertTestAuthors($count = 1)
    {
        $ids = [];
        for ($i = 1; $i <= $count; $i++) {
            $author = new TestORMAuthor(true);
            $id = $author->create([
                'name' => "Author {$i}",
                'email' => "author{$i}@test.com",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $ids[] = $id;
        }
        return $ids;
    }

    /**
     * Cleanup - Eliminar tablas al finalizar todos los tests
     */
    public static function tearDownAfterClass(): void
    {
        // Comentado para no eliminar las tablas automáticamente
        // Si quieres limpiar, descomenta estas líneas:
        // Schema::dropIfExists('test_orm_books');
        // Schema::dropIfExists('test_orm_authors');
    }
}

// ============================================
// MODELOS DE PRUEBA
// ============================================

/**
 * Modelo de prueba: TestORMAuthor
 */
class TestORMAuthor extends Model
{
    protected static $table = 'test_orm_authors';

    function __construct(bool $connect = false){
        parent::__construct($connect);
        $this->table_name = 'test_orm_authors';
    }
}

/**
 * Modelo de prueba: TestORMBook
 */
class TestORMBook extends Model
{
    protected static $table = 'test_orm_books';

    function __construct(bool $connect = false){
        parent::__construct($connect);
        $this->table_name = 'test_orm_books';
    }
}
