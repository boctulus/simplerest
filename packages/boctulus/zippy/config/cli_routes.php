<?php

use Boctulus\Simplerest\Core\CliRouter;

/*
    Zippy Package CLI Routes
*/

// Grouped commands under 'zippy'
CliRouter::group('zippy', function() {

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

    // Category commands
    CliRouter::group('category', function() {
        // php com zippy category list
        CliRouter::command('list', 'Boctulus\Zippy\Controllers\CategoryController@list_categories'); 
    });
});
