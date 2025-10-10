<?php

namespace Boctulus\Simplerest\tests;

use PHPUnit\Framework\TestCase;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (php_sapi_name() != "cli") {
  return;
}

// require_once __DIR__ . '../../vendor/autoload.php';
require_once __DIR__ . '/../app.php';


/*
 *
 * Requiere PHPUnit y una configuración adecuada de la base de datos.
 *
 * Ejecuta con: ./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/ExampleTestTest.php
 *
*/
class ExampleTestTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        /*
            Extra bootstrapping
        */
        // DB::setConnection('db_connection');
    }


   // test functions
   // ...
}
