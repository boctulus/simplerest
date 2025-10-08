<?php

use Boctulus\Simplerest\Core\CliRouter;

/*
    Zippy Package CLI Routes
*/

// Grouped commands under 'zippy'
CliRouter::group('zippy', function() {

    // Importer commands
    CliRouter::group('importer', function() {
        CliRouter::command('import', 'Boctulus\Zippy\Controllers\ProductImportController@import_zippy_csv');
        CliRouter::command('check-dupes', 'Boctulus\Zippy\Controllers\ProductImportController@check_dupes');
        CliRouter::command('index', 'Boctulus\Zippy\Controllers\ProductImportController@index');
    });

    // CSV reader commands
    CliRouter::group('csv', function() {
        CliRouter::command('comercio', 'Boctulus\Zippy\Controllers\ZippyController@read_csv_comercio');
        CliRouter::command('products', 'Boctulus\Zippy\Controllers\ZippyController@read_csv_products');
        CliRouter::command('sucursales', 'Boctulus\Zippy\Controllers\ZippyController@read_csv_sucursales');
    });

    // Users commands
    CliRouter::group('users', function() {
        CliRouter::command('index', 'Boctulus\Zippy\Controllers\ZippyUsersController@index');
        CliRouter::command('login', 'Boctulus\Zippy\Controllers\ZippyUsersController@login');
    });
});
