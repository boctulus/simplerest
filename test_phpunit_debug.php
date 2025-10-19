<?php

namespace Boctulus\Simplerest\tests;

require_once 'vendor/autoload.php';
require_once 'app.php';

use Boctulus\Simplerest\Core\Model;

echo "Test 1: Creating Product class instance...\n";

class Product extends Model
{
    protected static $table = 'products';

    function __construct(bool $connect = false)
    {
        echo "Product constructor called with connect=$connect\n";
        parent::__construct($connect, null, false);
        $this->table_name = static::$table;
        echo "Product constructor finished\n";
    }
}

echo "Test 2: newInstance...\n";
$product = Product::newInstance();
echo "newInstance OK\n";

echo "Test 3: Setting attributes...\n";
$product->name = 'Test Product';
$product->description = 'Test';
$product->slug = 'test-' . uniqid();
$product->images = '[]';
$product->cost = 100;
echo "Attributes OK\n";

echo "Test 4: Calling save...\n";
try {
    $product->save();
    echo "Save OK\n";
} catch (\Exception $e) {
    echo "Error in save: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
