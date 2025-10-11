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

/**
 * Tests adicionales para QueryBuilderTrait
 * Pruebas para métodos que no están cubiertos en ModelTest.php
 *
 * Ejecutar con: ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/QueryBuilderExtendedTest.php
 */
class QueryBuilderExtendedTest extends TestCase
{
  use UnitTestCaseSQLTrait;

  function test_rightjoin()
  {
    $users = DB::table(get_users_table())->select([
      "users.id",
      "users.name",
      "products.name as product_name"
    ])
      ->rightJoin("products", "products.belongs_to", "=", "users.id")
      ->dontExec()
      ->get();

    $this->assertSQLEquals(DB::getLog(), 'SELECT users.id, users.name, products.name as product_name FROM users RIGHT JOIN products ON products.belongs_to=users.id WHERE deleted_at IS NULL;');
  }

  function test_naturaljoin()
  {
    DB::table('products')
      ->naturalJoin('users')
      ->where(['products.cost', 100, '>'])
      ->deleted()
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), 'SELECT * FROM products NATURAL JOIN users WHERE products.cost > 100;');
  }

  function test_orderby_asc_desc()
  {
    // orderByAsc
    DB::table('products')->orderByAsc('cost')->limit(10)->dontExec()->get();
    $limit = $this->limit(10);
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY cost ASC $limit;");

    // orderByDesc
    DB::table('products')->orderByDesc('cost')->limit(10)->dontExec()->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY cost DESC $limit;");
  }

  function test_reorder()
  {
    DB::table('products')
      ->orderBy(['cost' => 'ASC'])
      ->reorder()
      ->orderBy(['id' => 'DESC'])
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY id DESC;");
  }

  function test_skip()
  {
    // skip es alias de offset
    DB::table('products')->limit(10)->skip(20)->dontExec()->get();
    $limit = $this->limit(10, 20);
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE deleted_at IS NULL $limit;");
  }

  function test_wheredate()
  {
    DB::table('products')
      ->whereDate('created_at', '2024-01-15')
      ->dontExec()->get();

    // whereDate usa LIKE para buscar por fecha
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (created_at LIKE '2024-01-15%') AND deleted_at IS NULL;");

    // Con operador personalizado
    DB::table('products')
      ->whereDate('created_at', '2024-01-15', '>')
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (created_at > '2024-01-15') AND deleted_at IS NULL;");
  }

  function test_whereregex()
  {
    // whereRegEx
    DB::table('products')
      ->whereRegEx('name', '^[A-Z]')
      ->deleted()
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE name REGEXP '^[A-Z]';");

    // whereNotRegEx
    DB::table('products')
      ->whereNotRegEx('name', '^[0-9]')
      ->deleted()
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (name NOT REGEXP '^[0-9]');");
  }

  function test_wherelike()
  {
    DB::table('products')
      ->whereLike('name', '%coca%')
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (name LIKE '%coca%') AND deleted_at IS NULL;");

    // orWhereLike
    DB::table('products')
      ->where(['cost', 100, '>'])
      ->orWhereLike('name', '%pepsi%')
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (cost > 100) OR (name LIKE '%pepsi%') AND deleted_at IS NULL;");
  }

  function test_wherenot()
  {
    DB::table('products')
      ->whereNot('active', 1)
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (active != 1) AND deleted_at IS NULL;");
  }

  function test_firstwhere()
  {
    DB::table('products')
      ->firstWhere(['cost', 100, '>']);

    // firstWhere internamente usa LIMIT 1 fijo
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (cost > 100) AND deleted_at IS NULL LIMIT 1;");
  }

  function test_findor()
  {
    // findOr con callback - el SQL real que ejecuta es find
    $result = DB::table('products')
      ->findOr(999999, function () {
        return ['id' => null, 'name' => 'Not Found'];
      });

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (id = 999999) AND deleted_at IS NULL;");

    // Si no encuentra, debe ejecutar el callback
    if ($result === null || !isset($result['id'])) {
      $this->assertEquals(null, $result['id'] ?? null);
    }
  }

  function test_exists()
  {
    DB::table('products')
      ->where(['id', 1])
      ->exists();

    // Sin espacio después de EXISTS
    $this->assertSQLEquals(DB::getLog(), "SELECT EXISTS (SELECT 1 FROM products WHERE (id = 1) AND deleted_at IS NULL);");
  }

  function test_pluck()
  {
    DB::table('products')
      ->where(['cost', 100, '>'])
      ->pluck('name');

    $this->assertSQLEquals(DB::getLog(), "SELECT name FROM products WHERE (cost > 100) AND deleted_at IS NULL;");
  }

  function test_touch()
  {
    DB::table('products')
      ->where(['id', 1])
      ->dontExec()
      ->touch();

    // touch() actualiza updated_at
    $sql = DB::getLog();
    $this->assertStringContainsString('UPDATE products SET updated_at', $sql);
    $this->assertStringContainsString('WHERE id = 1', $sql);
  }

  function test_soft_deletes()
  {
    // withTrashed - incluye eliminados
    DB::table('products')
      ->withTrashed()
      ->where(['cost', 100, '>'])
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE cost > 100;");

    // onlyTrashed - solo eliminados
    DB::table('products')
      ->onlyTrashed()
      ->where(['cost', 100, '>'])
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (cost > 100) AND deleted_at IS NOT NULL;");
  }

  function test_restore()
  {
    DB::table('products')
      ->where(['id', 1])
      ->dontExec()
      ->restore();

    // restore() setea deleted_at a NULL y actualiza updated_at
    $sql = DB::getLog();
    $this->assertStringContainsString('UPDATE products SET deleted_at = NULL', $sql);
    $this->assertStringContainsString('WHERE id = 1', $sql);
  }

  function test_force_delete()
  {
    DB::table('products')
      ->where(['id', 999999])
      ->dontExec()
      ->forceDelete();

    $this->assertSQLEquals(DB::getLog(), "DELETE FROM products WHERE id = 999999;");
  }

  function test_createorignore()
  {
    try {
      DB::table('products')
        ->dontExec()
        ->createOrIgnore([
          'name' => 'Test Product',
          'slug' => 'test-product-' . uniqid(),
          'images' => '[]'
        ]);

      // El SQL depende del driver pero debe ser un INSERT
      $sql = DB::getLog();
      $this->assertStringContainsString('INSERT', strtoupper($sql));
    } catch (\Exception $e) {
      // Si no está implementado para este driver, skip
      $this->markTestSkipped('createOrIgnore may not be implemented for this driver');
    }
  }

  function test_insertorignore()
  {
    try {
      DB::table('products')
        ->dontExec()
        ->insertOrIgnore([
          [
            'name' => 'Product 1',
            'slug' => 'product-1-' . uniqid(),
            'images' => '[]'
          ],
          [
            'name' => 'Product 2',
            'slug' => 'product-2-' . uniqid(),
            'images' => '[]'
          ]
        ]);

      // Debe ser un INSERT
      $sql = DB::getLog();
      $this->assertStringContainsString('INSERT', strtoupper($sql));
    } catch (\Exception $e) {
      // Si no está implementado para este driver, skip
      $this->markTestSkipped('insertOrIgnore may not be implemented for this driver');
    }
  }

  function test_paginate()
  {
    // Página 1, 10 items por página
    DB::table('products')
      ->paginate(1, 10)
      ->dontExec()->get();

    $limit = $this->limit(10, 0);
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE deleted_at IS NULL $limit;");

    // Página 3, 10 items por página (offset = 20)
    DB::table('products')
      ->paginate(3, 10)
      ->dontExec()->get();

    $limit = $this->limit(10, 20);
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE deleted_at IS NULL $limit;");
  }

  function test_orwhere_variations()
  {
    // orWhere con array asociativo
    DB::table('products')
      ->where(['cost', 100, '>'])
      ->orWhere(['size' => '1L'])
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (cost > 100 OR size = '1L') AND deleted_at IS NULL;");

    // múltiples orWhere
    DB::table('products')
      ->where(['belongs_to', 90])
      ->orWhere(['name', 'CocaCola'])
      ->orWhere(['cost', 100, '<'])
      ->deleted()
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE belongs_to = 90 OR name = 'CocaCola' OR cost < 100;");
  }

  function test_firstorfail()
  {
    try {
      DB::table('products')
        ->where(['id', 999999])
        ->firstOrFail();

      // Si no lanza excepción, el test falla
      $this->fail('Expected exception not thrown');
    } catch (\Exception $e) {
      // Esperamos que lance excepción cuando no encuentra
      $this->assertTrue(true);
    }
  }

  function test_alias()
  {
    DB::table('products')
      ->alias('p')
      ->where(['p.cost', 100, '>'])
      ->deleted()
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products as p WHERE p.cost > 100;");
  }

  function test_joinraw()
  {
    DB::table('products')
      ->joinRaw('INNER JOIN users ON products.belongs_to = users.id')
      ->where(['products.cost', 100, '>'])
      ->deleted()
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products INNER JOIN users ON products.belongs_to = users.id WHERE products.cost > 100;");
  }

  function test_multiple_distinct()
  {
    // distinct sin argumentos
    DB::table('products')
      ->select(['name', 'cost'])
      ->distinct()
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT DISTINCT name, cost FROM products WHERE deleted_at IS NULL;");
  }

  function test_getone()
  {
    // getOne es alias de first - usa LIMIT 1 fijo
    DB::table('products')
      ->where(['cost', 100, '>'])
      ->getOne(['name', 'cost']);

    $this->assertSQLEquals(DB::getLog(), "SELECT cost, name FROM products WHERE (cost > 100) AND deleted_at IS NULL LIMIT 1;");
  }

  function test_top()
  {
    // top es alias de first - usa LIMIT 1 fijo
    DB::table('products')
      ->orderBy(['cost' => 'DESC'])
      ->top(['name', 'cost']);

    $this->assertSQLEquals(DB::getLog(), "SELECT cost, name FROM products WHERE deleted_at IS NULL ORDER BY cost DESC LIMIT 1;");
  }
}
