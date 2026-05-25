<?php

namespace Boctulus\Simplerest\tests;

require_once __DIR__ . '/../../app.php';

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Model;

class PGSQLQueryBuilderTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        DB::getConnection('pg_test');

        DB::statement('CREATE TABLE IF NOT EXISTS pg_test_products (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL DEFAULT \'\',
            description TEXT,
            size VARCHAR(10),
            cost NUMERIC(10,2),
            belongs_to INTEGER DEFAULT 1,
            images TEXT DEFAULT \'[]\',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP DEFAULT NULL
        )');

        DB::statement('CREATE TABLE IF NOT EXISTS pg_test_categories (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL
        )');

        DB::statement('DELETE FROM pg_test_products');
        DB::statement('DELETE FROM pg_test_categories');
    }

    protected function qb(): Model
    {
        $m = new Model();
        $m->setConn(DB::getConnection('pg_test'));
        $m->table('pg_test_products');
        $m->setSoftDelete(false);
        $m->exec = true;
        return $m;
    }

    public function setUp(): void
    {
        DB::setConnection('pg_test');
    }

    public function tearDown(): void
    {
        DB::statement('DELETE FROM pg_test_products');
        DB::statement('DELETE FROM pg_test_categories');
    }

    // ─── DQL ─────────────────────────────────────

    public function test_get()
    {
        DB::statement("INSERT INTO pg_test_products (name, slug, cost, size, images)
            VALUES ('Product A', 'prod-a', 10.50, '1L', '[]')");

        $rows = $this->qb()->get();
        $this->assertCount(1, $rows);
        $this->assertEquals('Product A', $rows[0]['name']);
    }

    public function test_first()
    {
        DB::statement("INSERT INTO pg_test_products (name, slug, cost, size) VALUES
            ('First', 'first', 100, '1L'),
            ('Second', 'second', 200, '2L')");

        $row = $this->qb()->first();
        $this->assertNotNull($row);
        $this->assertEquals('First', $row['name']);
    }

    public function test_find()
    {
        DB::statement("INSERT INTO pg_test_products (name, slug, cost) VALUES ('FindMe', 'find-me', 50)");
        $rows = $this->qb()->get();
        $id = $rows[0]['id'];

        $rows2 = $this->qb()->find($id)->get();
        $this->assertNotEmpty($rows2);
        $this->assertEquals('FindMe', $rows2[0]['name']);
    }

    public function test_where_basic()
    {
        DB::statement("INSERT INTO pg_test_products (name, slug, cost, size) VALUES
            ('A', 'a', 100, '1L'),
            ('B', 'b', 200, '2L'),
            ('C', 'c', 300, '3L')");

        $rows = $this->qb()->where(['size' => '2L'])->get();
        $this->assertCount(1, $rows);
        $this->assertEquals('B', $rows[0]['name']);
    }

    public function test_where_operator()
    {
        DB::statement("INSERT INTO pg_test_products (name, slug, cost) VALUES
            ('A', 'a', 50), ('B', 'b', 100), ('C', 'c', 150)");

        $rows = $this->qb()->where([['cost', 100, '>=']])->get();
        $this->assertCount(2, $rows);
    }

    public function test_where_in()
    {
        DB::statement("INSERT INTO pg_test_products (name, slug, cost) VALUES
            ('A', 'a', 10), ('B', 'b', 20), ('C', 'c', 30)");

        $rows = $this->qb()->whereIn('cost', [10, 30])->get();
        $this->assertCount(2, $rows);
    }

    public function test_where_null()
    {
        DB::statement("INSERT INTO pg_test_products (name, slug, description) VALUES
            ('A', 'a', 'has desc'), ('B', 'b', NULL)");

        $rows = $this->qb()->whereNull('description')->get();
        $this->assertCount(1, $rows);
        $this->assertEquals('B', $rows[0]['name']);
    }

    public function test_where_not_null()
    {
        DB::statement("INSERT INTO pg_test_products (name, slug, description) VALUES
            ('A', 'a', 'has desc'), ('B', 'b', NULL)");

        $rows = $this->qb()->whereNotNull('description')->get();
        $this->assertCount(1, $rows);
        $this->assertEquals('A', $rows[0]['name']);
    }

    public function test_where_like()
    {
        DB::statement("INSERT INTO pg_test_products (name, slug) VALUES
            ('CocaCola', 'coca'), ('Pepsi', 'pepsi')");

        $rows = $this->qb()->whereLike('name', '%Coca%')->get();
        $this->assertCount(1, $rows);
        $this->assertEquals('CocaCola', $rows[0]['name']);
    }

    public function test_or_where()
    {
        DB::statement("INSERT INTO pg_test_products (name, slug, cost) VALUES
            ('A', 'a', 100), ('B', 'b', 500), ('C', 'c', 1000)");

        $rows = $this->qb()
            ->where(['cost', 100])
            ->orWhere(['cost', 1000])
            ->get();

        $this->assertCount(2, $rows);
    }

    // ─── ORDER / LIMIT ───────────────────────────

    public function test_order_by()
    {
        DB::statement("INSERT INTO pg_test_products (name, slug, cost) VALUES
            ('A', 'a', 300), ('B', 'b', 100), ('C', 'c', 200)");

        $rows = $this->qb()->orderBy(['cost' => 'ASC'])->get();
        $this->assertEquals('B', $rows[0]['name']);
        $this->assertEquals('C', $rows[1]['name']);
        $this->assertEquals('A', $rows[2]['name']);
    }

    public function test_limit()
    {
        DB::statement("INSERT INTO pg_test_products (name, slug) VALUES
            ('A', 'a'), ('B', 'b'), ('C', 'c')");

        $rows = $this->qb()->limit(2)->get();
        $this->assertCount(2, $rows);
    }

    // ─── DML ─────────────────────────────────────

    public function test_create()
    {
        $id = $this->qb()->create([
            'name' => 'New Product',
            'slug' => 'new-product',
            'cost' => 99.99,
            'images' => '[]',
        ]);

        $this->assertNotNull($id);
        $rows = $this->qb()->find($id)->get();
        $this->assertNotEmpty($rows);
        $this->assertEquals('New Product', $rows[0]['name']);
    }

    public function test_update()
    {
        $id = $this->qb()->create([
            'name' => 'Old', 'slug' => 'old',
            'cost' => 10, 'images' => '[]',
        ]);

        $affected = $this->qb()
            ->where(['id' => $id])
            ->update(['name' => 'Updated', 'cost' => 99]);

        $this->assertEquals(1, $affected);
        $rows = $this->qb()->find($id)->get();
        $this->assertNotEmpty($rows);
        $this->assertEquals('Updated', $rows[0]['name']);
    }

    public function test_delete()
    {
        $id = $this->qb()->create([
            'name' => 'ToDelete', 'slug' => 'to-delete', 'images' => '[]',
        ]);

        $result = $this->qb()->where(['id' => $id])->setSoftDelete(false)->delete();
        $this->assertNotNull($result);

        $remaining = $this->qb()->get();
        $this->assertCount(0, $remaining);
    }

    // ─── AGGREGATES ──────────────────────────────

    public function test_count()
    {
        $this->qb()->create(['name' => 'A', 'slug' => 'a', 'images' => '[]']);
        $this->qb()->create(['name' => 'B', 'slug' => 'b', 'images' => '[]']);

        $this->assertEquals(2, $this->qb()->count());
    }

    public function test_avg()
    {
        $this->qb()->create(['name' => 'A', 'slug' => 'a', 'cost' => 100, 'images' => '[]']);
        $this->qb()->create(['name' => 'B', 'slug' => 'b', 'cost' => 200, 'images' => '[]']);

        $this->assertEquals(150, (int)$this->qb()->avg('cost'));
    }

    public function test_sum()
    {
        $this->qb()->create(['name' => 'A', 'slug' => 'a', 'cost' => 100, 'images' => '[]']);
        $this->qb()->create(['name' => 'B', 'slug' => 'b', 'cost' => 200, 'images' => '[]']);

        $this->assertEquals(300, (int)$this->qb()->sum('cost'));
    }

    // ─── VALUE / PLUCK ───────────────────────────

    public function test_value()
    {
        $this->qb()->create(['name' => 'Target', 'slug' => 'tgt', 'images' => '[]']);

        $val = $this->qb()->where(['name' => 'Target'])->value('slug');
        $this->assertEquals('tgt', $val);
    }

    public function test_pluck()
    {
        $this->qb()->create(['name' => 'A', 'slug' => 'a', 'images' => '[]']);
        $this->qb()->create(['name' => 'B', 'slug' => 'b', 'images' => '[]']);

        $this->assertCount(2, $this->qb()->pluck('name'));
    }

    // ─── PAGINATION ──────────────────────────────

    public function test_paginate()
    {
        $this->qb()->create(['name' => 'A', 'slug' => 'a', 'images' => '[]']);
        $this->qb()->create(['name' => 'B', 'slug' => 'b', 'images' => '[]']);
        $this->qb()->create(['name' => 'C', 'slug' => 'c', 'images' => '[]']);

        $rows = $this->qb()->paginate(1, 2)->get();
        $this->assertCount(2, $rows);
    }

    // ─── TRANSACTIONS ────────────────────────────

    public function test_transaction_commit()
    {
        DB::beginTransaction();
        $id = $this->qb()->create([
            'name' => 'TX Test', 'slug' => 'tx-test', 'images' => '[]',
        ]);
        DB::commit();

        $rows = $this->qb()->find($id)->get();
        $this->assertNotEmpty($rows);
        $this->assertEquals('TX Test', $rows[0]['name']);
    }

    public function test_transaction_rollback()
    {
        DB::beginTransaction();
        $this->qb()->create([
            'name' => 'Rollback Me', 'slug' => 'rollback-me', 'images' => '[]',
        ]);
        DB::rollback();

        $this->assertCount(0, $this->qb()->where(['name' => 'Rollback Me'])->get());
    }

    // ─── JOINS ──────────────────────────────────

    public function test_join()
    {
        DB::statement("INSERT INTO pg_test_categories (name) VALUES ('Cat1')");
        $cats = DB::select('SELECT id FROM pg_test_categories ORDER BY id DESC LIMIT 1');
        $catId = (int)$cats[0]['id'];

        $this->qb()->create([
            'name' => 'Prod1', 'slug' => 'p1',
            'belongs_to' => $catId, 'images' => '[]',
        ]);

        $m = new Model();
        $m->setConn(DB::getConnection('pg_test'));
        $m->table('pg_test_products');
        $m->setSoftDelete(false);
        $m->exec = true;

        $rows = $m->join('pg_test_categories', 'pg_test_products.belongs_to', '=', 'pg_test_categories.id')
            ->select(['pg_test_products.name', 'pg_test_categories.name as cat_name'])
            ->get();

        $this->assertCount(1, $rows);
    }

    // ─── HELPERS ────────────────────────────────

    public function test_driver_info()
    {
        DB::getConnection('pg_test');
        $this->assertEquals('pgsql', DB::driver());
    }

    public function test_quote()
    {
        $this->assertEquals('"my_column"', DB::quote('my_column'));
    }

    public function test_select_raw()
    {
        $this->qb()->create(['name' => 'A', 'slug' => 'a', 'cost' => 100, 'images' => '[]']);

        $rows = $this->qb()->selectRaw('COUNT(*) as total')->get();
        $this->assertEquals(1, (int)$rows[0]['total']);
    }
}
