<?php declare(strict_types=1);

use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\TemporaryExceptionHandler;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (php_sapi_name() != "cli"){
	return; 
}

require_once __DIR__ . '/app.php';

////////////////////////////////////////////////

// Instancia de la clase temporal
$handler = new TemporaryExceptionHandler();

try {

    $res = DB::table('sp_permissions')->pluck('name');
    dd($res);


} catch (\Exception $e) {
    // Llama al método del trait
    $handler->exception_handler($e);
}