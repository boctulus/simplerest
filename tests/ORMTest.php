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
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Traits\UnitTestCaseSQLTrait;

/*
 * Test del ORM Layer básico implementado en la clase Model
 *
 * Este test verifica:
 * - find($id) - buscar por ID
 * - all() - obtener todos los registros
 * - save() - crear/actualizar registros
 * - deleteInstance() - eliminar registros
 * - Magic getters/setters (__get/__set)
 * - __callStatic proxy para query builder
 *
 * Ejecuta con: ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/ORMTest.php
 */

// Modelo de prueba para products - SIN schema para evitar bucles
class Product extends Model
{
    protected static $table = 'products';

    function __construct(bool $connect = false)
    {
        // NO pasar schema para evitar inicialización compleja
        parent::__construct($connect, null, false);

        // Solo setear el nombre de la tabla
        $this->table_name = static::$table;
    }
}

class ORMTest extends TestCase
{
    use UnitTestCaseSQLTrait;

    protected static $test_product_id;
    protected static $test_product_name = 'ORM Test Product';

    // NOTA: Los métodos setUpBeforeClass y tearDownAfterClass están comentados
    // porque causan problemas de memoria con ciertos schemas.
    // La limpieza se hace manualmente en cada test cuando es necesario.

    /*
    public static function setUpBeforeClass(): void
    {
        // Limpiar cualquier registro de prueba previo
        DB::table('products')
            ->where(['name' => self::$test_product_name])
            ->setSoftDelete(false)
            ->delete();
    }

    public static function tearDownAfterClass(): void
    {
        // Limpiar registros de prueba al final
        DB::table('products')
            ->where(['name' => self::$test_product_name])
            ->setSoftDelete(false)
            ->delete();
    }
    */

    /**
     * Test 1: Crear un nuevo registro usando ORM
     */
    public function test_01_create_new_instance()
    {
        $product = Product::newInstance();
        $product->name = self::$test_product_name;
        $product->description = 'Test description for ORM';
        $product->slug = 'orm-test-product-' . uniqid();
        $product->images = '[]';
        $product->cost = 100;
        $product->size = '1L';

        $result = $product->save();

        $this->assertNotNull($product->id);
        $this->assertTrue($product->exists());
        $this->assertEquals(self::$test_product_name, $product->name);

        // Guardar ID para siguientes tests
        self::$test_product_id = $product->id;

        return $product->id;
    }

    /**
     * Test 2: Buscar registro por ID usando find()
     * @depends test_01_create_new_instance
     */
    public function test_02_find_by_id($id)
    {
        $product = Product::find($id);

        $this->assertNotNull($product);
        $this->assertTrue($product->exists());
        $this->assertEquals($id, $product->id);
        $this->assertEquals(self::$test_product_name, $product->name);
        $this->assertEquals('Test description for ORM', $product->description);

        return $id;
    }

    /**
     * Test 3: Actualizar registro existente
     * @depends test_02_find_by_id
     */
    public function test_03_update_existing($id)
    {
        $product = Product::find($id);

        $this->assertNotNull($product);

        // Actualizar usando magic setter
        $product->description = 'Updated description via ORM';
        $product->cost = 150;

        $product->save();

        // Verificar que se actualizó
        $updated = Product::find($id);
        $this->assertEquals('Updated description via ORM', $updated->description);
        $this->assertEquals(150, $updated->cost);

        return $id;
    }

    /**
     * Test 4: Obtener todos los registros
     */
    public function test_04_all()
    {
        $products = Product::all();

        $this->assertIsArray($products);
        $this->assertNotEmpty($products);

        // Verificar que son instancias del modelo
        $this->assertInstanceOf(Product::class, $products[0]);
        $this->assertTrue($products[0]->exists());
    }

    /**
     * Test 5: Query builder proxy con where()->getModels()
     */
    public function test_05_where_get()
    {
        $products = Product::where(['name' => self::$test_product_name])->getModels();

        $this->assertIsArray($products);
        $this->assertNotEmpty($products);
        $this->assertInstanceOf(Product::class, $products[0]);
        $this->assertEquals(self::$test_product_name, $products[0]->name);
    }

    /**
     * Test 6: Query builder proxy con where()->firstModel()
     */
    public function test_06_where_first()
    {
        $product = Product::where(['name' => self::$test_product_name])->firstModel();

        $this->assertNotNull($product);
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals(self::$test_product_name, $product->name);
        $this->assertTrue($product->exists());
    }

    /**
     * Test 7: Magic getters
     */
    public function test_07_magic_getters()
    {
        $product = Product::find(self::$test_product_id);

        // Acceder a atributos usando magic getter
        $this->assertEquals(self::$test_product_name, $product->name);
        $this->assertEquals('Updated description via ORM', $product->description);
        $this->assertEquals(150, $product->cost);
    }

    /**
     * Test 8: Magic setters
     */
    public function test_08_magic_setters()
    {
        $product = Product::newInstance();

        // Asignar atributos usando magic setter
        $product->name = 'Magic Setter Test';
        $product->cost = 200;

        $this->assertEquals('Magic Setter Test', $product->name);
        $this->assertEquals(200, $product->cost);
    }

    /**
     * Test 9: Query builder con múltiples condiciones
     */
    public function test_09_complex_where()
    {
        $products = Product::where([
            ['cost', 100, '>='],
            ['name' => self::$test_product_name]
        ])->getModels();

        $this->assertIsArray($products);

        if (!empty($products)) {
            $this->assertInstanceOf(Product::class, $products[0]);
        }
    }

    /**
     * Test 10: Verificar que find retorna null si no existe
     */
    public function test_10_find_not_found()
    {
        $product = Product::find(999999999);

        $this->assertNull($product);
    }

    /**
     * Test 11: Eliminar registro
     * Este debe ser el último test que use el registro
     */
    public function test_11_delete_instance()
    {
        $product = Product::find(self::$test_product_id);

        $this->assertNotNull($product);

        // Eliminar (soft delete por defecto)
        $result = $product->deleteInstance(true);

        $this->assertTrue($result);
        $this->assertFalse($product->exists());

        // Verificar que ya no existe (con soft delete)
        $deleted = Product::find(self::$test_product_id);
        $this->assertNull($deleted);
    }

    /**
     * Test 12: Crear instancia con array en constructor
     */
    public function test_12_new_instance_with_attributes()
    {
        $product = Product::newInstance([
            'name' => 'Constructor Test',
            'cost' => 300,
            'size' => '2L'
        ]);

        $this->assertEquals('Constructor Test', $product->name);
        $this->assertEquals(300, $product->cost);
        $this->assertEquals('2L', $product->size);
        $this->assertFalse($product->exists());
    }

    /**
     * Test 13: Query builder - select con campos específicos
     */
    public function test_13_select_specific_fields()
    {
        // Crear un producto para este test
        $product = Product::newInstance();
        $product->name = self::$test_product_name . ' - Select Test';
        $product->description = 'Select test description';
        $product->slug = 'select-test-' . uniqid();
        $product->images = '[]';
        $product->cost = 123;
        $product->save();

        $products = Product::select(['id', 'name', 'cost'])
            ->where(['name' => self::$test_product_name . ' - Select Test'])
            ->getModels();

        $this->assertNotEmpty($products);
        $this->assertInstanceOf(Product::class, $products[0]);

        // Limpiar
        $products[0]->deleteInstance(false);
    }

    /**
     * Test 14: Query builder - orderBy
     */
    public function test_14_order_by()
    {
        $products = Product::orderBy(['cost' => 'DESC'])->limit(5)->getModels();

        $this->assertIsArray($products);

        if (!empty($products)) {
            $this->assertInstanceOf(Product::class, $products[0]);
        }
    }

    /**
     * Test 15: Crear y guardar en una línea
     */
    public function test_15_create_and_save()
    {
        $product = Product::newInstance([
            'name' => self::$test_product_name . ' - Quick Create',
            'description' => 'Quick create description',
            'slug' => 'quick-create-' . uniqid(),
            'images' => '[]',
            'cost' => 99
        ]);

        $product->save();

        $this->assertTrue($product->exists());
        $this->assertNotNull($product->id);

        // Limpiar
        $product->deleteInstance(false);
    }
}
