<?php

namespace Boctulus\AzSeeder;

use Boctulus\Simplerest\Core\Libs\DB;

class AZSeederService
{
    const TEST_USER_EMAIL = 'tester3@g.c';
    const TEST_USER_PASSWORD = 'gogogo';

    protected $conn;
    protected $seededIds = [];

    public function __construct(string $conn = 'az')
    {
        $this->conn = $conn;
    }

    public function seedProducts(int $count = 10): array
    {
        DB::setConnection($this->conn);

        $uid = $this->getTestUserId();
        $now = date('Y-m-d H:i:s');
        $ids = [];

        for ($i = 1; $i <= $count; $i++) {
            $slug = "test-product-{$i}-" . uniqid();

            $id = DB::insert(
                "INSERT INTO products (name, type, regular_price, sale_price, description, short_description, slug, images, categories, tags, dimensions, sku, status, stock, stock_status, posted, comment, created_at, cost, size, belongs_to, active, locked)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    "Test Product {$i}",
                    $i % 2 === 0 ? 'simple' : 'variable',
                    number_format(10 + $i, 2, '.', ''),
                    $i % 3 === 0 ? number_format(8 + $i, 2, '.', '') : null,
                    "Description for test product {$i}",
                    "Short desc {$i}",
                    $slug,
                    '[]',
                    $i % 2 === 0 ? 'electronics' : 'clothing',
                    "test,seeded,product-{$i}",
                    json_encode(['weight' => 0.5 * $i, 'width' => 10, 'height' => 5]),
                    "SKU-" . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'publish',
                    100 + $i,
                    'instock',
                    1,
                    $i % 4 === 0 ? null : "Comment for product {$i}",
                    $now,
                    5 + ($i * 0.5),
                    $i % 2 === 0 ? 'M' : 'L',
                    $uid,
                    1,
                    0,
                ]
            );

            $ids[] = $id;
        }

        $this->seededIds = $ids;

        return $ids;
    }

    public function seedProduct(array $overrides = []): int
    {
        DB::setConnection($this->conn);

        $uid = $this->getTestUserId();
        $now = date('Y-m-d H:i:s');
        $i = count($this->seededIds) + 1;

        $data = array_merge([
            'name'             => "Test Product {$i}",
            'type'             => 'simple',
            'regular_price'    => '19.99',
            'sale_price'       => null,
            'description'      => "Test product description",
            'short_description'=> null,
            'slug'             => "test-product-" . uniqid(),
            'images'           => '[]',
            'categories'       => null,
            'tags'             => null,
            'dimensions'       => null,
            'sku'              => null,
            'status'           => 'publish',
            'stock'            => 50,
            'stock_status'     => 'instock',
            'posted'           => 1,
            'comment'          => null,
            'created_at'       => $now,
            'cost'             => 10.99,
            'size'             => null,
            'belongs_to'       => $uid,
            'active'           => 1,
            'locked'           => 0,
        ], $overrides);

        $fields = array_keys($data);
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $columns = implode(', ', $fields);
        $values = array_values($data);

        $id = DB::insert("INSERT INTO products ({$columns}) VALUES ({$placeholders})", $values);
        $this->seededIds[] = $id;

        return $id;
    }

    public function seedSoftDeletedProducts(int $count = 3): array
    {
        DB::setConnection($this->conn);

        $uid = $this->getTestUserId();
        $now = date('Y-m-d H:i:s');
        $ids = [];

        for ($i = 1; $i <= $count; $i++) {
            $id = DB::insert(
                "INSERT INTO products (name, description, slug, images, cost, belongs_to, created_at, deleted_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    "Deleted Product {$i}",
                    "Soft deleted product {$i}",
                    "deleted-product-" . uniqid(),
                    '[]',
                    10.99 + $i,
                    $uid,
                    $now,
                    $now,
                ]
            );

            $ids[] = $id;
        }

        $this->seededIds = array_merge($this->seededIds, $ids);

        return $ids;
    }

    public function cleanup(): void
    {
        DB::setConnection($this->conn);

        if (!empty($this->seededIds)) {
            DB::statement(
                "DELETE FROM products WHERE id IN (" . implode(',', array_fill(0, count($this->seededIds), '?')) . ")",
                $this->seededIds
            );

            $this->seededIds = [];
        }
    }

    public function cleanupAll(): void
    {
        DB::setConnection($this->conn);

        $uid = $this->getTestUserId();

        DB::statement("DELETE FROM products WHERE belongs_to = ?", [$uid]);

        $this->seededIds = [];
    }

    public function countProducts(): int
    {
        DB::setConnection($this->conn);

        $uid = $this->getTestUserId();

        $result = DB::select("SELECT COUNT(*) as cnt FROM products WHERE belongs_to = ? AND deleted_at IS NULL", [$uid]);

        return $result[0]['cnt'] ?? 0;
    }

    protected function getTestUserId(): int
    {
        DB::setConnection('main');

        $user = DB::select("SELECT id FROM users WHERE email = ?", [self::TEST_USER_EMAIL]);

        DB::setConnection($this->conn);

        if (empty($user)) {
            throw new \RuntimeException("Test user " . self::TEST_USER_EMAIL . " not found");
        }

        return $user[0]['id'];
    }
}
