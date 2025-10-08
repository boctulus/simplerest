<?php

use Boctulus\Simplerest\Core\WebRouter;

/*
    Zippy Package Routes
*/

// ZippyController routes
WebRouter::get('zippy/csv/comercio', 'Boctulus\Zippy\Controllers\ZippyController@read_csv_comercio');
WebRouter::get('zippy/csv/products', 'Boctulus\Zippy\Controllers\ZippyController@read_csv_products');
WebRouter::get('zippy/csv/sucursales', 'Boctulus\Zippy\Controllers\ZippyController@read_csv_sucursales');

// ProductImportController routes (ZippyProductsController.php)
WebRouter::get('zippy/importer', 'Boctulus\Zippy\Controllers\ProductImportController@index'); 
WebRouter::get('zippy/importer/import', 'Boctulus\Zippy\Controllers\ProductImportController@import_zippy_csv');
WebRouter::get('zippy/importer/check-dupes', 'Boctulus\Zippy\Controllers\ProductImportController@check_dupes');

// ZippyUsersController routes
WebRouter::get('zippy/users', 'Boctulus\Zippy\Controllers\ZippyUsersController@index'); 
WebRouter::get('zippy/users/login', 'Boctulus\Zippy\Controllers\ZippyUsersController@login');
