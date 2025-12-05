<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app.php';

use Boctulus\Simplerest\Core\Libs\DB;

echo "Test 1: select(['size'])->distinct()\n";
$query = DB::table('products')->select(['size'])->distinct();
echo $query->dd() . "\n\n";

echo "Test 2: distinct()->select(['size'])\n";
$query2 = DB::table('products')->distinct()->select(['size']);
echo $query2->dd() . "\n\n";

echo "Test 3: distinct(['size'])\n";
$query3 = DB::table('products')->distinct(['size']);
echo $query3->dd() . "\n\n";
