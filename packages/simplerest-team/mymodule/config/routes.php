<?php

/**
 * MyModule Routes
 *
 * Este archivo define las rutas del módulo myModule
 */

use Boctulus\Simplerest\Core\Route;
use SimplerestTeam\Mymodule\Controllers\TestController;

// Rutas web
Route::get('mymodule', [TestController::class, 'index']);
Route::get('mymodule/about', [TestController::class, 'about']);

// Rutas API
Route::group(['prefix' => 'api/mymodule'], function() {
    Route::get('data', [TestController::class, 'getData']);
    Route::post('data', [TestController::class, 'store']);
    Route::get('about', [TestController::class, 'about']);
});
