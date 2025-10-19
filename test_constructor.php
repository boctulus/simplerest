<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '128M');

require_once 'vendor/autoload.php';
require_once 'app.php';

class TestProduct extends \Boctulus\Simplerest\Core\Model
{
    protected static $table = 'products';
    function __construct(bool $connect = false)
    {
        echo 'Constructor called' . PHP_EOL;
        parent::__construct($connect, \Boctulus\Simplerest\Schemas\main\ProductsSchema::class, false);
        echo 'Constructor finished' . PHP_EOL;
    }
}

echo 'Creating instance with newInstance...' . PHP_EOL;
$p = TestProduct::newInstance(['name' => 'test'], false);
echo 'Instance created' . PHP_EOL;
echo 'exists method: ';
var_dump($p->exists());
echo 'name: ';
var_dump($p->name);
