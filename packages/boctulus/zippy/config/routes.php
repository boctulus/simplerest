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
WebRouter::get('zippy/vea', 'Boctulus\Zippy\Controllers\ProductImportController@index');  // deberia ir al final, es la ruta mas general del "grupo"
WebRouter::get('zippy/vea/import', 'Boctulus\Zippy\Controllers\ProductImportController@import_zippy_csv');
WebRouter::get('zippy/vea/check-dupes', 'Boctulus\Zippy\Controllers\ProductImportController@check_dupes');

// ZippyUsersController routes
WebRouter::get('zippy/users', 'Boctulus\Zippy\Controllers\ZippyUsersController@index'); // deberia ir al final, es la ruta mas general del "grupo"
WebRouter::get('zippy/users/login', 'Boctulus\Zippy\Controllers\ZippyUsersController@login');
