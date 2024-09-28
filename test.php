<?php declare(strict_types=1);

use simplerest\core\libs\Strings;
use simplerest\core\libs\TemporaryExceptionHandler;

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
    
    $content = '```json { "review": "El paragolpes delantero para el Nissan Patrol GR Y61 es simplemente excepcional. Su diseño robusto y la calidad de los materiales aseguran una gran durabilidad. Además, el hecho de que esté listo para soldar facilita su instalación. ¡Una compra altamente recomendada para quienes buscan mejorar la protección de su vehículo!" } ```';

    if (preg_match('/```json\s*(.+?)\s*```/s', $content, $matches)) {
        // Extraemos el contenido del JSON capturado
        $json_string = $matches[1];
        
        // Decodificamos el JSON para manipularlo como un array o un objeto
        $json_data = json_decode($json_string, true);
    
        // Mostramos el JSON decodificado
        print_r($json_data);
    } else {
        echo "No se encontró ningún contenido JSON.";
    }

} catch (\Exception $e) {
    // Llama al método del trait
    $handler->exception_handler($e);
}