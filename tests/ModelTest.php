<?php

namespace simplerest\tests;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '../../vendor/autoload.php';

if (php_sapi_name() != "cli") {
  return;
}

require_once __DIR__ . '/../app.php';

use PHPUnit\Framework\TestCase;
use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\Model;
use simplerest\core\traits\UnitTestCaseSQLTrait;
use simplerest\core\libs\Validator;

class ModelTest extends TestCase
{
  use UnitTestCaseSQLTrait;

  function test_get()
  {
    //
    $query = DB::table('products');
    $this->assertSQLEquals($query->dd(), 'SELECT * FROM products WHERE deleted_at IS NULL;');

    //
    $query = DB::table('products')->deleted();
    $this->assertSQLEquals($query->dd(), 'SELECT * FROM products;');

    //  
    $query = DB::table('products')->select(['size'])->distinct();
    $this->assertSQLEquals($query->dd(), 'SELECT DISTINCT size FROM products WHERE deleted_at IS NULL;');

    // 
    $query = DB::table('products')->select(['size', 'cost'])->distinct();
    $this->assertSQLEquals($query->dd(), 'SELECT DISTINCT size, cost FROM products WHERE deleted_at IS NULL;');

    // 
    $query = DB::table('products')->oldest();
    $this->assertSQLEquals($query->dd(), 'SELECT * FROM products WHERE deleted_at IS NULL ORDER BY created_at DESC;');

    // 
    $query = DB::table('products')->newest();
    $this->assertSQLEquals($query->dd(), 'SELECT * FROM products WHERE deleted_at IS NULL ORDER BY created_at ASC;');

    //  
    $query = DB::table('products')->random()->select(['id', 'name'])->get();
    $rand = $this->rand_fn();
    $this->assertSQLEquals(DB::getLog(), "SELECT id, name FROM products WHERE deleted_at IS NULL ORDER BY $rand;");

    $query = DB::table('products')->random()->select(['id', 'name'])->limit(5)->get();
    $limit = $this->limit(5);
    $this->assertSQLEquals(DB::getLog(), "SELECT id, name FROM products WHERE deleted_at IS NULL ORDER BY $rand $limit;");

    DB::table('products')->deleted()
      ->select(['id', 'name', 'size', 'cost', 'belongs_to'])
      ->where(['size', '1L'])
      ->orderBy(['size' => 'desc', 'cost' => 'asc'])
      ->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT id, name, size, cost, belongs_to FROM products WHERE size = '1L' ORDER BY size DESC, cost ASC;");

    // 
    $query = DB::table(get_users_table())
      ->where(['id' => 160])
      ->count();
    $this->assertSQLEquals(DB::getLog(), 'SELECT COUNT(*) FROM users WHERE (id = 160) AND deleted_at IS NULL;');

    //  
    DB::table('products')->deleted()
      ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
      ->count('updated_at');
    $this->assertSQLEquals(DB::getLog(), "SELECT COUNT(updated_at) FROM products WHERE (cost >= 100 AND size = '1L' AND belongs_to = 90);");

    //  
    DB::table('products')->deleted()
      ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
      ->distinct()
      ->count('description');
    $this->assertSQLEquals(DB::getLog(), "SELECT COUNT(DISTINCT description) FROM products WHERE (cost >= 100 AND size = '1L' AND belongs_to = 90);");

    //  
    $query = DB::table('products')
      ->where([['cost', 200, '>='], ['size', '2L']], 'OR')
      ->count();
    $this->assertSQLEquals(DB::getLog(), "SELECT COUNT(*) FROM products WHERE (cost >= 200 OR size = '2L') AND deleted_at IS NULL;");

    //  
    $query = DB::table('products')->deleted()
      ->where([['cost', 200, '>='], ['size', '2L']], 'OR')
      ->count();
    $this->assertSQLEquals(DB::getLog(), "SELECT COUNT(*) FROM products WHERE (cost >= 200 OR size = '2L');");

    //  
    DB::table('products')
      ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
      ->distinct()
      ->count('description');
    $this->assertSQLEquals(DB::getLog(), "SELECT COUNT(DISTINCT description) FROM products WHERE (cost >= 100 AND size = '1L' AND belongs_to = 90) AND deleted_at IS NULL;");

    // 
    DB::table('products')
      ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
      ->avg('cost');
    $this->assertSQLEquals(DB::getLog(), "SELECT AVG(cost) FROM products WHERE (cost >= 100 AND size = '1L' AND belongs_to = 90) AND deleted_at IS NULL;");

    // 
    DB::table('products')
      ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
      ->sum('cost');
    $this->assertSQLEquals(DB::getLog(), "SELECT SUM(cost) FROM products WHERE (cost >= 100 AND size = '1L' AND belongs_to = 90) AND deleted_at IS NULL;");

    //  
    DB::table('products')
      ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
      ->min('cost');
    $this->assertSQLEquals(DB::getLog(), "SELECT MIN(cost) FROM products WHERE (cost >= 100 AND size = '1L' AND belongs_to = 90) AND deleted_at IS NULL;");

    // 
    DB::table('products')
      ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
      ->max('cost');
    $this->assertSQLEquals(DB::getLog(), "SELECT MAX(cost) FROM products WHERE (cost >= 100 AND size = '1L' AND belongs_to = 90) AND deleted_at IS NULL;");

    // 
    DB::table('products')->orderBy(['cost' => 'DESC'])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY cost DESC;");

    // 
    DB::table('products')->limit(10)->offset(20)->get();
    $limit = $this->limit(10, 20);
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE deleted_at IS NULL $limit;");

    // 
    DB::table('products')
      ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
      ->limit(10)
      ->offset(20)
      ->get(['cost']);

    $limit = $this->limit(10, 20);
    $this->assertSQLEquals(DB::getLog(), "SELECT cost FROM products WHERE (cost >= 100 AND size = '1L' AND belongs_to = 90) AND deleted_at IS NULL $limit;");

    // 
    DB::table('products')->random()->select(['id', 'name'])->addSelect('cost')->first();
    $rand = $this->rand_fn();
    $this->assertSQLEquals(DB::getLog(), "SELECT id, name, cost FROM products WHERE deleted_at IS NULL ORDER BY $rand;");

    //
    DB::table('products')->setFetchMode('COLUMN')
      ->selectRaw('cost * ? as cost_after_inc', [1.05])->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT cost * 1.05 as cost_after_inc FROM products WHERE deleted_at IS NULL;");

    // 
    DB::table('products')
      ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
      ->selectRaw('cost * ? as cost_after_inc', [1.05])->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT cost * 1.05 as cost_after_inc FROM products WHERE (cost >= 100 AND size = '1L' AND belongs_to = 90) AND deleted_at IS NULL;");

    //  
    DB::table('products')
      ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
      ->selectRaw('cost * ? as cost_after_inc', [1.05])->distinct()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT DISTINCT cost * 1.05 as cost_after_inc FROM `products` WHERE (cost >= 100 AND size = '1L' AND belongs_to = 90) AND deleted_at IS NULL");

    // 
    DB::table('products')
      ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
      ->select(['name', 'size'])
      ->selectRaw('cost * ? as cost_after_inc', [1.05])->distinct()->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT DISTINCT cost * 1.05 as cost_after_inc, name, size FROM products WHERE (cost >= 100 AND size = '1L' AND belongs_to = 90) AND deleted_at IS NULL;");

    // 
    DB::table('products')
      ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
      ->selectRaw('cost * ? as cost_after_inc', [1.05])->distinct()
      ->addSelect('name')
      ->addSelect('size')
      ->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT DISTINCT cost * 1.05 as cost_after_inc, name, size FROM products WHERE (cost >= 100 AND size = '1L' AND belongs_to = 90) AND deleted_at IS NULL;");

    //   
    DB::table('products')
      ->where([['cost', 100, '>='], ['size', '1L'], ['belongs_to', 90]])
      ->selectRaw('cost * ? as cost_after_inc', [1.05])
      ->addSelect('name')
      ->addSelect('cost')
      ->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT cost * 1.05 as cost_after_inc, name, cost FROM products WHERE (cost >= 100 AND size = '1L' AND belongs_to = 90) AND deleted_at IS NULL;");

    // 
    DB::table('products')->deleted()
      ->groupBy(['size'])->select(['size'])->count();
    $this->assertSQLEquals(DB::getLog(), "SELECT size, COUNT(*) FROM products GROUP BY size;");

    // 
    DB::table('products')->deleted()
      ->groupBy(['size'])->select(['size'])
      ->avg('cost');
    $this->assertSQLEquals(DB::getLog(), "SELECT size, AVG(cost) FROM products GROUP BY size;");

    // 
    DB::table('products')->deleted()->where([
      ['size', '2L']
    ])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE size = '2L';");

    // 
    DB::table('products')->deleted()->where([
      'size',
      '2L'
    ])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE size = '2L';");

    //  
    DB::table('products')->deleted()->where(
      ['size' => '2L']
    )->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE size = '2L';");

    // 
    DB::table('products')
      ->where([
        ['name', ['Vodka', 'Wisky', 'Tekila', 'CocaCola']], // IN 
        ['locked', 0],
        ['belongs_to', 90]
      ])
      ->whereNotNull('description')
      ->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE ((name IN ('Vodka', 'Wisky', 'Tekila', 'CocaCola') AND locked = 0 AND belongs_to = 90) AND description IS NOT NULL) AND deleted_at IS NULL;");

    // 
    DB::table('products')->where([
      ['name', ['CocaCola', 'PesiLoca']],
      ['cost', 550, '>='],
      ['cost', [100, 200]]
    ], 'OR')->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (name IN ('CocaCola', 'PesiLoca') OR cost IN (100, 200) OR cost >= 550) AND deleted_at IS NULL;");

    // 
    DB::table('products')->where([
      ['name', ['CocaCola', 'PesiLoca', 'Wisky', 'Vodka'], 'NOT IN']
    ])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (name NOT IN ('CocaCola', 'PesiLoca', 'Wisky', 'Vodka')) AND deleted_at IS NULL;");

    //  
    DB::table('products')->where([
      ['cost', 200, '<'],
      ['name', 'CocaCola']
    ])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (cost < 200 AND name = 'CocaCola') AND deleted_at IS NULL;");

    //  
    DB::table('products')->where([
      ['cost', 200, '>='],
      ['cost', 270, '<=']
    ])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (cost >= 200 AND cost <= 270) AND deleted_at IS NULL;");

    //  
    DB::table('products')->where(['size', ['0.5L', '3L'], 'IN'])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (size IN ('0.5L', '3L')) AND deleted_at IS NULL;");

    // 
    DB::table('products')->where(['size', ['0.5L', '3L']])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (size IN ('0.5L', '3L')) AND deleted_at IS NULL;");

    //  
    DB::table('products')->whereIn('size', ['0.5L', '3L'])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (size IN ('0.5L', '3L')) AND deleted_at IS NULL;");

    // 
    DB::table('products')->where(['size', ['0.5L', '3L'], 'NOT IN'])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (size NOT IN ('0.5L', '3L')) AND deleted_at IS NULL;");

    // 
    DB::table('products')->whereNotIn('size', ['0.5L', '3L'])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (size NOT IN ('0.5L', '3L')) AND deleted_at IS NULL;");

    //  
    DB::table('products')->where(['workspace', null])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (workspace IS NULL) AND deleted_at IS NULL;");

    //  
    DB::table('products')->whereNull('workspace')->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (workspace IS NULL) AND deleted_at IS NULL;");

    // 
    DB::table('products')->where(['workspace', null, 'IS NOT'])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (workspace IS NOT NULL) AND deleted_at IS NULL;");

    //  
    DB::table('products')->whereNotNull('workspace')->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (workspace IS NOT NULL) AND deleted_at IS NULL;");

    // 
    DB::table('products')
      ->select(['name', 'cost'])
      ->whereBetween('cost', [100, 250])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT name, cost FROM products WHERE (cost >= 100 AND cost <= 250) AND deleted_at IS NULL;");

    //  
    DB::table('products')
      ->select(['name', 'cost'])
      ->whereNotBetween('cost', [100, 250])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT name, cost FROM products WHERE (cost < 100 OR cost > 250) AND deleted_at IS NULL;");

    //  
    DB::table('products')
      ->find(103)
      ->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (id = 103) AND deleted_at IS NULL;");

    // 
    DB::table('products')
      ->where(['cost', 150])
      ->limit(1)
      ->value('name');

    $limit = $this->limit(1);
    $this->assertSQLEquals(DB::getLog(), "SELECT name FROM products WHERE (cost = 150) AND deleted_at IS NULL $limit;");

    //  
    DB::table('products')->deleted()
      ->select(['name', 'cost', 'id'])
      ->where(['belongs_to', 90])
      ->where([
        ['cost', 100, '>='],
        ['cost', 500, '<']
      ])
      ->whereNotNull('description')
      ->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT name, cost, id FROM products WHERE belongs_to = 90 AND (cost >= 100 AND cost < 500) AND description IS NOT NULL;");

    //   
    DB::table('products')->deleted()
      ->select(['name', 'cost', 'id'])
      ->where(['belongs_to', 90])
      ->where([
        ['name', ['CocaCola', 'PesiLoca']],
        ['cost', 550, '>='],
        ['cost', 100, '<']
      ], 'OR')
      ->whereNotNull('description')
      ->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT name, cost, id FROM products WHERE belongs_to = 90 AND (name IN ('CocaCola', 'PesiLoca') OR cost >= 550 OR cost < 100) AND description IS NOT NULL;");

    // 
    DB::table('products')->deleted()
      ->select(['name', 'cost', 'id'])
      ->where(['belongs_to', 90])
      ->orWhere(['name', ['CocaCola', 'PesiLoca']])
      ->orWhere([
        ['cost', 550, '<='],
        ['cost', 100, '>=']
      ])
      ->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT name, cost, id FROM products WHERE belongs_to = 90 OR name IN ('CocaCola', 'PesiLoca') OR (cost <= 550 AND cost >= 100);");

    //  
    DB::table('products')->deleted()
      ->select(['name', 'cost', 'id', 'description'])
      ->whereNotNull('description')
      ->orWhere([
        ['cost', 100, '>='],
        ['cost', 500, '<']
      ])
      ->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT name, cost, id, description FROM products WHERE description IS NOT NULL OR (cost >= 100 AND cost < 500);");

    // 
    DB::table('products')
      ->select(['id', 'name', 'cost', 'description'])
      ->where(['belongs_to', 90])
      ->where([
        ['name', ['CocaCola', 'PesiLoca']],
        ['cost', 550, '>='],
        ['cost', 100, '<']
      ], 'OR')
      ->whereNotNull('description')
      ->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT id, name, cost, description FROM products WHERE (belongs_to = 90 AND (name IN ('CocaCola', 'PesiLoca') OR cost >= 550 OR cost < 100) AND description IS NOT NULL) AND deleted_at IS NULL;");

    //  
    DB::table(get_users_table())->unhide(['password'])
      ->where([
        'email' => 'nano@g.c',
        'username' => 'nano'
      ], 'OR')
      ->setValidator((new Validator())->setRequired(false))
      ->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM users WHERE (email = 'nano@g.c' OR username = 'nano') AND deleted_at IS NULL;");

    //  
    $res = DB::table(get_users_table())
      ->select(['id', 'username'])
      ->where(['email' => 'nano@g.c'])
      ->orWhere(['username' => 'nano'])
      ->deleted()
      ->get();

    // Debería chequear solo la parte del WHERE
    $this->assertSQLEquals(DB::getLog(), "SELECT id, username FROM users WHERE email = 'nano@g.c' OR username = 'nano';");

    //  
    $rows = DB::table(get_users_table())
      ->select(['id', 'username'])
      ->where(['email' => 'nano@g.c'])
      ->orWhere(['username' => 'nano'])
      //->deleted()
      ->get();

    // Debería chequear solo la parte del WHERE
    $this->assertSQLEquals(DB::getLog(), "SELECT id, username FROM users WHERE (email = 'nano@g.c' OR username = 'nano') AND deleted_at IS NULL;");

    /*

    // falla en PSQL
    DB::table('products')
    ->where(['belongs_to' => 90])
    ->whereRaw('cost < IF(size = "1L", ?, 100) AND size = ?', [300, '1L'])
    ->orderBy(['cost' => 'ASC'])
    ->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE ((cost < IF(size = \"1L\", 300, 100) AND size = '1L') AND belongs_to = 90) AND deleted_at IS NULL ORDER BY cost ASC;");

    */

    // 
    DB::table('products')->deleted()
      ->whereRaw('EXISTS (SELECT 1 FROM users WHERE products.belongs_to = users.id AND users.lastname = ?  )', ['AB'])
      ->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE EXISTS (SELECT 1 FROM users WHERE products.belongs_to = users.id AND users.lastname = 'AB' );");

    // 
    DB::table('products')->deleted()
      ->whereExists('(SELECT 1 FROM users WHERE products.belongs_to = users.id AND users.lastname = ?)', ['AB'])
      ->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE EXISTS (SELECT 1 FROM users WHERE products.belongs_to = users.id AND users.lastname = 'AB');");

    // 
    DB::table('products')->orderBy(['cost' => 'ASC', 'id' => 'DESC'])->take(4)->offset(1)->get();

    $limit = $this->limit(4, 1);
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY cost ASC, id DESC $limit;");

    // 
    DB::table('products')->orderBy(['cost' => 'ASC'])->orderBy(['id' => 'DESC'])->take(4)->offset(1)->get();

    $limit = $this->limit(4, 1);
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY cost ASC, id DESC $limit;");

    // S
    DB::table('products')->orderBy(['cost' => 'ASC'])->take(4)->offset(1)->get(null, ['id' => 'DESC']);
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY cost ASC, id DESC $limit;");

    // 
    DB::table('products')->orderBy(['cost' => 'ASC'])->orderBy(['id' => 'DESC'])->take(4)->offset(1)->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY cost ASC, id DESC $limit;");

    // 
    DB::table('products')->take(4)->offset(1)->get(null, ['cost' => 'ASC', 'id' => 'DESC']);
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY cost ASC, id DESC $limit;");

    // 
    DB::table('products')->orderByRaw('locked * active DESC')->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY locked * active DESC;");

    // 
    DB::table('products')->where([
      ['cost', 100, '>=']
    ])->orderBy(['size' => 'DESC'])->groupBy(['size'])->select(['size'])->avg('cost');
    $this->assertSQLEquals(DB::getLog(), "SELECT size, AVG(cost) FROM products WHERE (cost >= 100) AND deleted_at IS NULL GROUP BY size ORDER BY size DESC;");

    //  
    DB::table('products')->where([
      ['cost', 200, '>='],
      ['cost', 270, '<='],
      ['belongs_to', 90]
    ])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (cost >= 200 AND cost <= 270 AND belongs_to = 90) AND deleted_at IS NULL;");

    //  
    DB::table('products')
      ->where([
        ['cost', 150, '>='],
        ['cost', 270, '<=']
      ])
      ->where(['belongs_to' => 90])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE ((cost >= 150 AND cost <= 270) AND belongs_to = 90) AND deleted_at IS NULL;");

    //  
    DB::table('products')->where(['workspace', null])->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (workspace IS NULL) AND deleted_at IS NULL;");

    //  
    DB::table('products')->whereNull('workspace')->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM products WHERE (workspace IS NULL) AND deleted_at IS NULL;");

    //  
    DB::table('products')->deleted()
      ->select(['id', 'name', 'size', 'cost', 'belongs_to'])
      ->whereRaw('belongs_to IN (SELECT id FROM users WHERE password IS NULL)')
      ->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT id, name, size, cost, belongs_to FROM products WHERE belongs_to IN (SELECT id FROM users WHERE password IS NULL);");

  }

  function test_having()
  {
    if (DB::driver() == 'mysql') {
      DB::table('products')->deleted()
        ->groupBy(['name'])
        ->having(['c', 3, '>'])
        ->select(['name'])
        ->selectRaw('COUNT(*) as c')
        ->get();

      $this->assertSQLEquals(DB::getLog(), "SELECT name, COUNT(*) as c FROM products GROUP BY name HAVING c > 3;");

      DB::table('products')
        ->groupBy(['name'])
        ->having(['c', 3, '>='])
        ->select(['name'])
        ->selectRaw('COUNT(name) as c')
        ->get();

      $this->assertSQLEquals(DB::getLog(), "SELECT name, COUNT(name) as c FROM products WHERE deleted_at IS NULL GROUP BY name HAVING c >= 3;");
    }

    // 
    DB::table('products')
      ->groupBy(['cost', 'size'])
      ->having(['cost', 100])
      ->get(['cost', 'size']);

    $this->assertSQLEquals(DB::getLog(), "SELECT cost, size FROM products WHERE deleted_at IS NULL GROUP BY cost,size HAVING cost = 100;");

    // 
    DB::table('products')->deleted()
      ->groupBy(['cost', 'size'])
      ->having(['cost', 100])
      ->get(['cost', 'size']);

    $this->assertSQLEquals(DB::getLog(), "SELECT cost, size FROM products GROUP BY cost,size HAVING cost = 100;");

    // 
    DB::table('products')->deleted()
      ->groupBy(['cost', 'size', 'belongs_to'])
      ->having(['belongs_to', 90])
      ->having(
        [
          ['cost', 100, '>='],
          ['size' => '1L']
        ],
        'OR'
      )
      ->orderBy(['size' => 'DESC'])
      ->get(['cost', 'size', 'belongs_to']);

    $this->assertSQLEquals(DB::getLog(), "SELECT cost, size, belongs_to FROM products GROUP BY cost,size,belongs_to HAVING belongs_to = 90 AND (cost >= 100 OR size = '1L') ORDER BY size DESC;");
  }

  function test_orHaving()
  {
    // 
    DB::table('products')->deleted()
      ->groupBy(['cost', 'size', 'belongs_to'])
      ->having(['belongs_to', 90])
      ->orHaving(['cost', 100, '>='])
      ->orHaving(['size' => '1L'])
      ->orderBy(['size' => 'DESC'])
      ->get(['cost', 'size', 'belongs_to']);

    $this->assertSQLEquals(DB::getLog(), "SELECT cost, size, belongs_to FROM products GROUP BY cost,size,belongs_to HAVING belongs_to = 90 OR cost >= 100 OR size = '1L' ORDER BY size DESC;");

    // 
    DB::table('products')->deleted()
      ->groupBy(['cost', 'size', 'belongs_to'])
      ->having(['belongs_to', 90])
      ->orHaving(
        [
          ['cost', 100, '>='],
          ['size' => '1L']
        ]
      )
      ->orderBy(['size' => 'DESC'])
      ->get(['cost', 'size', 'belongs_to']);

    $this->assertSQLEquals(DB::getLog(), "SELECT cost, size, belongs_to FROM products GROUP BY cost,size,belongs_to HAVING belongs_to = 90 OR (cost >= 100 AND size = '1L') ORDER BY size DESC;");

    // 
    DB::table('products')->deleted()
      ->groupBy(['cost', 'size', 'belongs_to'])
      ->having(['belongs_to', 90])
      ->or(function ($q) {
        $q->having(['cost', 100, '>='])
          ->having(['size' => '1L']);
      })
      ->orderBy(['size' => 'DESC'])
      ->dontExec()
      ->get(['cost', 'size', 'belongs_to']);

    $this->assertSQLEquals(DB::getLog(), "SELECT cost, size, belongs_to FROM products GROUP BY cost,size,belongs_to HAVING belongs_to = 90 OR (cost >= 100 AND size = '1L') ORDER BY size DESC;");
  }

  function test_havingRaw()
  {
    DB::table('products')
      ->selectRaw('SUM(cost) as total_cost')
      ->where(['size', '1L'])
      ->groupBy(['belongs_to'])
      ->havingRaw('SUM(cost) > ?', [500])
      ->limit(3)
      ->offset(1)
      ->get();

    $limit = $this->limit(3, 1);
    $this->assertSQLEquals(DB::getLog(), "SELECT SUM(cost) as total_cost FROM products WHERE (size = '1L') AND deleted_at IS NULL GROUP BY belongs_to HAVING SUM(cost) > 500 $limit;");

    // 
    DB::table('products')->deleted()
      ->groupBy(['cost', 'size', 'belongs_to'])
      ->havingRaw('SUM(cost) > ?', [500])
      ->or(function ($q) {
        $q->having(['cost', 100, '>='])
          ->having(['size' => '1L']);
      })
      ->orderBy(['size' => 'DESC'])
      ->dontExec()
      ->get(['cost', 'size', 'belongs_to']);

    $this->assertSQLEquals(DB::getLog(), "SELECT cost, size, belongs_to FROM products GROUP BY cost,size,belongs_to HAVING (SUM(cost) > 500) OR (cost >= 100 AND size = '1L') ORDER BY size DESC;");

    // 
    DB::table('products')->deleted()
      ->groupBy(['cost', 'size', 'belongs_to'])
      ->having(['cost', 100, '>='])
      ->or(function ($q) {
        $q->havingRaw('SUM(cost) > ?', [500])
          ->having(['size' => '1L']);
      })
      ->orderBy(['size' => 'DESC'])
      ->dontExec()
      ->get(['cost', 'size', 'belongs_to']);

    $this->assertSQLEquals(DB::getLog(), "SELECT cost, size, belongs_to FROM products GROUP BY cost,size,belongs_to HAVING cost >= 100 OR ((SUM(cost) > 500) AND size = '1L') ORDER BY size DESC;");
  }

  function test_inner_join()
  {
    //    
    $m = (new Model())->table('other_permissions', 'op')
      ->join('folders', 'op.folder_id', '=', 'folders.id')
      ->join('users', 'folders.belongs_to', '=', 'users.id')
      ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
      ->where([
        ['guest', 1],
        ['table', 'products'],
        ['r', 1]
      ])
      ->orderByRaw('users.id DESC')
      ->dontExec();

    $this->assertSQLEquals($m->dd(), "SELECT * FROM other_permissions as op INNER JOIN folders ON op.folder_id=folders.id INNER JOIN users ON folders.belongs_to=users.id INNER JOIN user_roles ON users.id=user_roles.user_id WHERE (guest = 1 AND table = 'products' AND r = 1) ORDER BY users.id DESC;");
  }

  /* 
      Con \PDO::ATTR_EMULATE_PREPARES => false, detecta si los campos son existen o no en la DB
      durante el prepare()

      PDOException: SQLSTATE[42S02]: Base table or view not found: 1146 Table 'az.countries' doesn't exist

  */
  function test_leftjoin()
  {
    $current = DB::getCurrent();

    $mysql = in_array(DB::driver(), ['mysql', 'sqlite']);
    $emul_false = isset($current['pdo_options'][\PDO::ATTR_EMULATE_PREPARES]) && $current['pdo_options'][\PDO::ATTR_EMULATE_PREPARES] == false;

    $dr = [$mysql, $emul_false];

    switch ($dr) {
      case [true, true]:
        $this->assertSQLEquals(true, true);
        return;
      case [true, false]:
      case [false, false]:
      case [false, true]:
        $users = DB::table(get_users_table())->select([
          "users.id",
          "users.name",
          "users.email",
          "countries.name as country_name"
        ])
          ->leftJoin("countries", "countries.id", "=", "users.country_id")
          ->dontExec()
          ->get();

        $this->assertSQLEquals(DB::getLog(), 'SELECT users.id, users.name, users.email, countries.name as country_name FROM users LEFT JOIN countries ON countries.id=users.country_id WHERE deleted_at IS NULL;');
        break;
    }
  }

  function test_crossjoin()
  {
    DB::table(get_users_table())
      ->crossJoin('products')
      ->where(['users.id', 90])
      ->unhideAll()
      ->deleted()
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), 'SELECT * FROM users CROSS JOIN products WHERE users.id = 90;');

    DB::table(get_users_table())->crossJoin('products')->crossJoin('roles')
      ->unhideAll()
      ->deleted()
      ->dontExec()->count();

    $this->assertSQLEquals(DB::getLog(), 'SELECT COUNT(*) FROM users CROSS JOIN products CROSS JOIN roles;');

    DB::table(get_users_table())->crossJoin('products')->crossJoin('roles')
      ->where(['users.id', 90])
      ->unhideAll()
      ->deleted()
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), 'SELECT * FROM users CROSS JOIN products CROSS JOIN roles WHERE users.id = 90;');

    DB::table(get_users_table())->crossJoin('products')->crossJoin('roles')
      ->join('user_sp_permissions', 'users.id', '=', 'user_sp_permissions.user_id')
      ->unhideAll()
      ->deleted()
      ->dontExec()->count();

    $this->assertSQLEquals(DB::getLog(), 'SELECT COUNT(*) FROM users CROSS JOIN products CROSS JOIN roles INNER JOIN user_sp_permissions ON users.id=user_sp_permissions.user_id;');
  }


  function test_or_whereraw()
  {
    $m = DB::table('products')

      ->where([
        ['cost', 50, '>'], // AND
        ['id', 190, '<=']
      ])
      // AND
      ->group(function ($q) {
        $q->where(['active', 1])
          // OR
          ->orWhereRaw('name LIKE ?', ['%a%']);
      })
      // AND
      ->where(['belongs_to', 1, '>'])
      ->select(['id', 'name', 'cost', 'size', 'description', 'belongs_to'])
      ->deleted()
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT id, name, cost, size, description, belongs_to FROM products WHERE (cost > 50 AND id <= 190) AND (active = 1 OR (name LIKE '%a%')) AND belongs_to > 1;");

  }

  function test_subqueries()
  {
    //  
    $sub = DB::table(get_users_table())
      ->select(['id'])
      ->whereRaw('password IS NULL');

    $rows = DB::table('products')->deleted()
      ->select(['id', 'name', 'size', 'cost', 'belongs_to'])
      ->whereRaw("belongs_to IN ({$sub->toSql()})")
      ->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT id, name, size, cost, belongs_to FROM products WHERE belongs_to IN (SELECT id FROM users WHERE (password IS NULL) AND deleted_at IS NULL);");

    //  
    $sub = DB::table(get_users_table())->deleted()
      ->select(['id'])
      ->whereRaw('confirmed_email = 1')
      ->where(['password', 100, '<']);

    $res = DB::table('products')->deleted()
      ->mergeBindings($sub)
      ->select(['id', 'name', 'size', 'cost', 'belongs_to'])
      ->where(['size', '1L'])
      ->whereRaw("belongs_to IN ({$sub->toSql()})")
      ->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT id, name, size, cost, belongs_to FROM products WHERE (belongs_to IN (SELECT id FROM users WHERE (confirmed_email = 1) AND password < 100)) AND size = '1L';");

    // 
    $sub = DB::table(get_users_table())->deleted()
      ->selectRaw('users.id')
      ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
      ->whereRaw('confirmed_email = 1')
      ->where(['password', 100, '<'])
      ->where(['role_id', 2]);

    $res = DB::table('products')->deleted()
      ->mergeBindings($sub)
      ->select(['id', 'name', 'size', 'cost', 'belongs_to'])
      ->where(['size', '1L'])
      ->whereRaw("belongs_to IN ({$sub->toSql()})")
      ->orderBy(['id' => 'desc'])
      ->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT id, name, size, cost, belongs_to FROM products WHERE (belongs_to IN (SELECT users.id FROM users INNER JOIN user_roles ON users.id=user_roles.user_id WHERE (confirmed_email = 1) AND password < 100 AND role_id = 2)) AND size = '1L' ORDER BY id DESC;");

    //  
    $sub = DB::table(get_users_table())->deleted()
      ->selectRaw('users.id')
      ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
      ->whereRaw('confirmed_email = 1')
      ->where(['password', 100, '<'])
      ->where(['role_id', 3]);

    $res = DB::table('products')->deleted()
      ->mergeBindings($sub)
      ->select(['size'])
      ->whereRaw("belongs_to IN ({$sub->toSql()})")
      ->groupBy(['size'])
      ->avg('cost');

    $this->assertSQLEquals(DB::getLog(), "SELECT size, AVG(cost) FROM products WHERE belongs_to IN (SELECT users.id FROM users INNER JOIN user_roles ON users.id=user_roles.user_id WHERE (confirmed_email = 1) AND password < 100 AND role_id = 3) GROUP BY size;");

    //
    $sub = DB::table('products')->deleted()
      ->select(['size'])
      ->groupBy(['size']);

    $m = new Model(true);
    $res = $m->fromRaw("({$sub->toSql()}) as sub")->count();

    $this->assertSQLEquals(trim(preg_replace('!\s+!', ' ', $m->getLastPrecompiledQuery())), "SELECT COUNT(*) FROM (SELECT size FROM products GROUP BY size) as sub");

    //
    $sub = DB::table('products')->deleted()
      ->select(['size'])
      ->where(['belongs_to', 90])
      ->groupBy(['size']);

    $res = DB::table("({$sub->toSql()}) as sub")
      ->mergeBindings($sub)
      ->count();

    $this->assertSQLEquals(trim(preg_replace('!\s+!', ' ', DB::getLog())), "SELECT COUNT(*) FROM (SELECT size FROM products WHERE belongs_to = 90 GROUP BY size) as sub;");
  }


  function test_union()
  {
    // 
    $uno = DB::table('products')->deleted()
      ->select(['id', 'name', 'description', 'belongs_to'])
      ->where(['belongs_to', 90]);

    $m2 = DB::table('products')->deleted();
    $dos = $m2
      ->select(['id', 'name', 'description', 'belongs_to'])
      ->where(['belongs_to', 4])
      ->where(['cost', 200, '>='])
      ->union($uno)
      ->orderBy(['id' => 'ASC'])
      ->get();

    $this->assertSQLEquals(preg_replace('!\s+!', ' ', $m2->getLastPrecompiledQuery()), "SELECT id, name, description, belongs_to FROM products WHERE belongs_to = ? AND cost >= ? UNION SELECT id, name, description, belongs_to FROM products WHERE belongs_to = ? ORDER BY id ASC");
  }

  function test_delete()
  {
    $u = DB::table(get_users_table());
    $u->where(['id' => 100000])->dontExec()->setSoftDelete(false)->delete();
    $this->assertSQLEquals(DB::getLog(), "DELETE FROM users WHERE id = 100000;");
  }

  function test_hide()
  {
    $unhide = ['password'];
    $hide = ['username', 'confirmed_email', 'firstname', 'lastname', 'deleted_at', 'belongs_to'];

    $u = DB::table(get_users_table());
    $u->unhide($unhide);
    $u->hide($hide);
    $u->where(['id' => 100000])->get();

    $sql = DB::getLog();
    $this->assertSQLEquals(Strings::contains('password', $sql), true);
    $this->assertSQLEquals(!Strings::contains('firstname', $sql), true);
  }

  function test_fill1()
  {
    $u = DB::table(get_users_table());

    $id = $u->create(['username' => 'testing', 'email' => 'testing@g.com', 'password' => 'pass', 'firstname' => 'Jhon', 'lastname' => 'Doe', 'confirmed_email' => 1]);
    $res = DB::table(get_users_table())->unhide(['password'])->latest()->first();

    $this->assertNotNull($res['password']);

    // Clean-up
    $u
      ->where(['email' => 'testing@g.com'])
      ->delete(false);
  }

  function test_fill2()
  {
    $u = DB::table(get_users_table());

    $u->unfill(['password']);
    $id = $u->create(['username' => 'testing', 'email' => 'testing@g.com', 'password' => 'pass', 'firstname' => 'Jhon', 'lastname' => 'Doe']);
    $res = DB::table(get_users_table())->unhide(['password'])->latest()->first();

    $this->assertNull($res['password']);

    // Clean-up
    $u
      ->where(['email' => 'testing@g.com'])
      ->delete(false);
  }


  function test_when()
  {
    $fn = function ($lastname) {
      DB::table(get_users_table())
        ->when($lastname, function ($q) use ($lastname) {
          $q->where(['lastname', $lastname]);
        })
        ->unhideAll() // fuerzo '*'
        ->dontExec()->get();
    };


    $fn('Bozzo');
    // debe contener lastname
    $this->assertSQLEquals(DB::getLog(), 'SELECT * FROM users WHERE (lastname = \'Bozzo\') AND deleted_at IS NULL;');

    $fn(NULL);
    // *no* debe contener lastname
    $this->assertSQLEquals(DB::getLog(), 'SELECT * FROM users WHERE deleted_at IS NULL;');

    $fn('');
    // *no* debe contener lastname
    $this->assertSQLEquals(DB::getLog(), 'SELECT * FROM users WHERE deleted_at IS NULL;');


    $fn = function ($sortBy) {
      DB::table('products')
        ->when($sortBy, function ($q) use ($sortBy) {
          $q->orderBy($sortBy);
        }, function ($q) {
          $q->orderBy(['id' => 'DESC']);
        })
        ->dontExec()->get();
    };

    $fn(['name' => 'ASC']);
    $this->assertSQLEquals(DB::getLog(), 'SELECT * FROM products WHERE deleted_at IS NULL ORDER BY name ASC;');

    $fn(NULL);
    $this->assertSQLEquals(DB::getLog(), 'SELECT * FROM products WHERE deleted_at IS NULL ORDER BY id DESC;');

    $fn([]);
    $this->assertSQLEquals(DB::getLog(), 'SELECT * FROM products WHERE deleted_at IS NULL ORDER BY id DESC;');
  }

  function test_wherecol()
  {
    DB::table(get_users_table())
      ->whereColumn('firstname', 'lastname', '=')
      ->unhideAll()
      ->deleted()
      ->dontExec()->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM `users` WHERE firstname=lastname");

    DB::table(get_users_table())
      ->whereColumn('firstname', 'lastname', '!=')
      ->unhideAll()
      ->deleted()
      ->dontExec()->get();
    $this->assertSQLEquals(DB::getLog(), "SELECT * FROM `users` WHERE firstname!=lastname");
  }


  function test_groups()
  {
    // group
    $m = (new Model())
      ->table('products')

      ->where([
        ['cost', 100, '>'], // AND
        ['id', 50, '<']
      ])
      // AND
      ->whereRaw('name LIKE ?', ['%a'])
      // AND
      ->group(function ($q) {
        $q->where(['active', 1])
          // OR
          ->orWhere([
            ['cost', 100, '<='],
            ['description', NULL, 'IS NOT']
          ]);
      })
      // AND
      ->where(['belongs_to', 150, '>'])
      ->select(['id', 'cost', 'size', 'description', 'belongs_to']);

    $this->assertSQLEquals($m->dontExec()->dd(), "SELECT id, cost, size, description, belongs_to FROM products WHERE (name LIKE '%a') AND (cost > 100 AND id < 50) AND (active = 1 OR (cost <= 100 AND description IS NOT NULL)) AND belongs_to > 150;");

    // or
    $m = (new Model())
      ->table('products')

      ->where([
        ['cost', 100, '>'], // AND
        ['id', 50, '<']
      ])
      // OR
      ->or(function ($q) {
        $q->whereRaw('name LIKE ?', ['%a'])
          // AND  
          ->where([
            ['cost', 100, '<='],
            ['description', NULL, 'IS NOT']
          ]);
      })
      // AND
      ->where(['belongs_to', 150, '>'])

      ->select(['id', 'cost', 'size', 'description', 'belongs_to']);

    $this->assertSQLEquals($m->dontExec()->dd(), "SELECT id, cost, size, description, belongs_to FROM products WHERE (cost > 100 AND id < 50) OR ((name LIKE '%a') AND (cost <= 100 AND description IS NOT NULL)) AND belongs_to > 150;");


    // not
    DB::table('products')

      ->not(function ($q) {  // <-- group *
        $q->where([
          ['cost', 100, '>'],
          ['id', 50, '<']
        ])
          // OR
          ->orWhere([
            ['cost', 100, '<='],
            ['description', NULL, 'IS NOT']
          ]);
      })
      // AND
      ->where(['belongs_to', 150, '>'])
      ->select(['id', 'cost', 'size', 'description', 'belongs_to'])
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), "SELECT id, cost, size, description, belongs_to FROM products WHERE (NOT ((cost > 100 AND id < 50) OR (cost <= 100 AND description IS NOT NULL)) AND belongs_to > 150) AND deleted_at IS NULL;");


    // not or
    DB::table('products')

      ->where(['belongs_to', 150, '>'])
      ->not(function ($q) {
        $q->where(['name', 'a$'])
          ->or(function ($q) {
            $q->where([
              ['cost', 100, '<='],
              ['description', NULL, 'IS NOT']
            ]);
          });
      })
      ->dontExec()
      ->where(['size', '1L', '>='])
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), 'SELECT * FROM products WHERE (belongs_to > 150 AND NOT (name = \'a$\' OR ((cost <= 100 AND description IS NOT NULL))) AND size >= \'1L\') AND deleted_at IS NULL;');

    // not or
    $m = DB::table('products')

      ->where(['belongs_to', 150, '>'])
      ->not(function ($q) {
        $q->where([
          ['cost', 100, '<='],
          ['description', NULL, 'IS NOT']
        ])
          ->or(function ($q) {
            $q->whereRegEx('name', 'a$');
          });
      })
      ->dontExec()
      ->where(['size', '1L', '>=']);


    // group y or
    $m = DB::table('products')

      ->where(['belongs_to', 150, '>'])
      ->not(function ($q) {
        $q->where([
          ['cost', 100, '<='],
          ['description', NULL, 'IS NOT']
        ])
          ->or(function ($q) {
            $q->whereRegEx('name', 'a$');
          });
      })
      ->where(['size', '1L', '>='])
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), 'SELECT * FROM products WHERE (belongs_to > 150 AND NOT ((cost <= 100 AND description IS NOT NULL) OR (name REGEXP \'a$\')) AND size >= \'1L\') AND deleted_at IS NULL;');


    DB::table('products')

      ->where(['belongs_to', 150, '>'])
      ->not(function ($q) {
        $q->whereRegEx('name', 'a$')
          ->or(function ($q) {  // <-------  metería un 'AND' sino fuera por un parche
            $q->where([
              ['cost', 100, '<='],
              ['description', NULL, 'IS NOT']
            ]);
          });
      })
      ->where(['size', '1L', '>='])
      ->dontExec()->get();

    $this->assertSQLEquals(DB::getLog(), 'SELECT * FROM products WHERE (belongs_to > 150 AND NOT ((name REGEXP \'a$\') OR ((cost <= 100 AND description IS NOT NULL))) AND size >= \'1L\') AND deleted_at IS NULL;');


    $m = DB::table('products')

      ->whereRegEx('name', 'a$')
      ->or(function ($q) {  // <-------  metería un 'AND' sino fuera por un parche
        $q->where(['cost', 100, '<=']);
      })
      ->deleted()
      ->dontExec();

    $this->assertSQLEquals($m->dd(), 'SELECT * FROM products WHERE (name REGEXP \'a$\') OR (cost <= 100);');
  }

  function test_update()
  {
    $u = DB::table(get_users_table());
    $u->where(['id' => 100000])
      ->update(['firstname' => 'Nico', 'lastname' => 'Buzzi']);
    $this->assertSQLEquals(DB::getLog(), "UPDATE `users` SET firstname = 'Nico', lastname = 'Buzzi', updated_at = '2024-12-22 16:20:27' WHERE id = 100000");

    $u->where([['lastname', ['AAA', 'Buzzi']]])
      ->update(['firstname' => 'Nicolay']);
    $this->assertSQLEquals(DB::getLog(), "UPDATE users SET firstname = 'Nicolay', updated_at = '2024-12-22 16:20:27'  WHERE id = 100000 AND lastname IN ('AAA', 'Buzzi');");

    $u = DB::table(get_users_table());
    $u->where(['id' => 100000])
      ->update(['firstname' => NULL]);
    $this->assertSQLEquals(DB::getLog(), "UPDATE users SET firstname = NULL, updated_at = '2024-12-22 16:20:27' WHERE id = 100000;");
  }

  function test_update_2() {
    // Limpieza previa
    DB::table('products')->where(['name' => 'Test Product'])->setSoftDelete(false)->delete();
    
    // Crear registro para actualizar
    $id = DB::table('products')->create([
        'name' => 'Test Product',
        'description' => 'Initial description',
        'slug' => 'test-product-' . uniqid(),
        'images' => '[]'
    ]);

    // Actualizar registro
    DB::table('products')->where(['id' => $id])->update([
        'description' => 'Updated description',
        'cost' => '99.99'
    ]);

    $this->assertSQLEquals(
        DB::getLog(),
        "UPDATE products SET description = 'Updated description', cost = '99.99', updated_at = '2025-01-06 10:00:00' WHERE id = $id;"
    );

    // Limpiar
    DB::table('products')->where(['id' => $id])->setSoftDelete(false)->delete();
}

  function test_create() {
      // Limpieza previa
      DB::table('products')->where(['name' => 'New Product'])->setSoftDelete(false)->delete();

      // Crear nuevo registro
      $data = [
          'name' => 'New Product',
          'description' => 'Test description',
          'slug' => 'new-product-' . uniqid(),
          'images' => '[]'          
      ];

      $id = DB::table('products')->create($data);
      
      $this->assertSQLEquals(
          DB::getLog(),
          "INSERT INTO products (name, description, slug, images, created_at) VALUES ('New Product', 'Test description', '" . $data['slug'] . "', '[]', '2025-01-06 10:00:00');"
      );

      // Limpiar
      DB::table('products')->where(['id' => $id])->setSoftDelete(false)->delete();
  }

  function test_createOrUpdate() {
      // Limpieza previa
      $slug = 'test-product-' . uniqid();
      DB::table('products')->where(['slug' => $slug])->setSoftDelete(false)->delete();

      // Primer insert
      $update_data = [
          'name' => 'Test Product',
          'description' => 'Updated description', 
          'images' => '[]'
      ];

      DB::table('products')->createOrUpdate($update_data, ['slug']);
      
      $this->assertSQLEquals(
          DB::getLog(),
          "INSERT INTO products (name, description, slug, images, created_at) VALUES ('Test Product', 'Initial description', '$slug', '[]', '2025-01-06 10:00:00');"
      );

      // Update del mismo registro
      $data['description'] = 'Updated description';
      $id = DB::table('products')->createOrUpdate($data, ['slug']);

      $this->assertSQLEquals(
          DB::getLog(),
          "UPDATE products SET name = 'Test Product', description = 'Updated description', images = '[]', created_at = '2025-01-06 10:00:00', updated_at = '2025-01-06 10:00:00' WHERE slug = '$slug';"
      );

      // Limpiar
      DB::table('products')->where(['id' => $id])->setSoftDelete(false)->delete();
  }


}
