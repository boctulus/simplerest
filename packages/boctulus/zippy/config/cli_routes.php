<?php

use Boctulus\Simplerest\Core\CliRouter;
use Boctulus\Zippy\Strategies\LLMMatchingStrategy;

/*
    Zippy Package CLI Routes

    NOTE: Los comandos de categorías y Ollama han sido migrados a ZippyCommand
    Usar: php com zippy <comando>

    Ejecuta `php com zippy help` para ver todos los comandos disponibles
*/

// Grouped commands under 'zippycart'
 CliRouter::group('zippycart', function() {

    // Importer commands
    CliRouter::group('importer', function() {
        CliRouter::command('import:coto', 'Boctulus\Zippy\Controllers\CotoProductImportController@import_zippy_csv');
        CliRouter::command('check_dupes:coto', 'Boctulus\Zippy\Controllers\CotoProductImportController@check_dupes');
        CliRouter::command('index:coto', 'Boctulus\Zippy\Controllers\CotoProductImportController@index');

        CliRouter::command('import:carrefour', 'Boctulus\Zippy\Controllers\CarrefourProductImportController@import_zippy_csv');
        CliRouter::command('check_dupes:carrefour', 'Boctulus\Zippy\Controllers\CarrefourProductImportController@check_dupes');
        CliRouter::command('index:carrefour', 'Boctulus\Zippy\Controllers\CarrefourProductImportController@index');
    });

    // CSV reader commands
    CliRouter::group('csv', function() {
        CliRouter::command('comercio', 'Boctulus\Zippy\Controllers\CSVTestController@read_csv_comercio');
        CliRouter::command('products', 'Boctulus\Zippy\Controllers\CSVTestController@read_csv_products');
        CliRouter::command('sucursales', 'Boctulus\Zippy\Controllers\CSVTestController@read_csv_sucursales');
    });

    // Users commands
    CliRouter::group('users', function() {
        CliRouter::command('index', 'Boctulus\Zippy\Controllers\FirebaseTestController@index');
        CliRouter::command('login', 'Boctulus\Zippy\Controllers\FirebaseTestController@login');
    });

    // Nuevos comandos de procesamiento de productos
    CliRouter::group('products', function() {
        CliRouter::command('process_categories', 'Boctulus\Zippy\Controllers\ProductCategoryController@process_products');
        CliRouter::command('process_uncategorized', 'Boctulus\Zippy\Controllers\ProductCategoryController@process_uncategorized');
    });

    // Category commands - DEPRECATED: Migrados a ZippyCommand
    // Usar: php com zippy category_* en su lugar
    CliRouter::group('category', function() {
        // Existing
        CliRouter::command('import', 'Boctulus\Zippy\Controllers\AdminTasksController@insertCategories');
    });
});
