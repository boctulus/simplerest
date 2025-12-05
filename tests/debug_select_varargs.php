<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app.php';

use Boctulus\Simplerest\Core\Libs\DB;

echo "Test con array:\n";
$query1 = DB::table('products')->select(['size', 'cost'])->distinct();
echo "SQL: " . $query1->dd() . PHP_EOL;
echo "Expected: SELECT DISTINCT size, cost FROM products WHERE deleted_at IS NULL\n\n";

echo "Test con múltiples argumentos (como Laravel):\n";
$query2 = DB::table('products')->select('size', 'cost')->distinct();
echo "SQL: " . $query2->dd() . PHP_EOL;  
echo "Expected: SELECT DISTINCT size, cost FROM products WHERE deleted_at IS NULL\n\n";
