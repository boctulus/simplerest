<?php

use Boctulus\Simplerest\Core\WebRouter;

/*
    Zippy Package Routes
*/

// ZippyController routes
WebRouter::get('zippy/csv/comercio', 'Boctulus\Zippy\Controllers\ZippyController@read_csv_comercio');
WebRouter::get('zippy/csv/products', 'Boctulus\Zippy\Controllers\ZippyController@read_csv_products');
WebRouter::get('zippy/csv/sucursales', 'Boctulus\Zippy\Controllers\ZippyController@read_csv_sucursales');

// CotoProductImportController routes (ZippyProductsController.php)
WebRouter::get('zippy/importer/coto', 'Boctulus\Zippy\Controllers\CotoProductImportController@index'); 
WebRouter::get('zippy/importer/coto/import', 'Boctulus\Zippy\Controllers\CotoProductImportController@import_zippy_csv');
WebRouter::get('zippy/importer/coto/check_dupes', 'Boctulus\Zippy\Controllers\CotoProductImportController@check_dupes');

// ZippyUsersController routes
WebRouter::get('zippy/users', 'Boctulus\Zippy\Controllers\ZippyUsersController@index'); 
WebRouter::get('zippy/users/login', 'Boctulus\Zippy\Controllers\ZippyUsersController@login');
