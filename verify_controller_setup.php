<?php
/*
 * Script para verificar si las rutas del paquete friendlypos-web están siendo procesadas
 */

require_once __DIR__ . '/app.php';

echo "Verificando si el framework puede encontrar el controlador OpenFacturaController...\n\n";

echo "Verificando si la clase OpenFacturaController existe: ";
if (class_exists('Boctulus\FriendlyposWeb\Controllers\OpenFacturaController')) {
    echo "SI\n";
    
    echo "Verificando si el método health existe: ";
    if (method_exists('Boctulus\FriendlyposWeb\Controllers\OpenFacturaController', 'health')) {
        echo "SI\n";
        
        echo "Probando la creación de una instancia del controlador...\n";
        try {
            $controller = new \Boctulus\FriendlyposWeb\Controllers\OpenFacturaController();
            echo "Instancia creada exitosamente.\n";
            
            echo "Probando el método health directamente...\n";
            // Este test puede fallar si el método requiere propiedades inicializadas
            // o si hay problemas con el entorno
            try {
                $reflection = new \ReflectionMethod($controller, 'health');
                echo "El método health existe y es accesible.\n";
            } catch (Exception $e) {
                echo "Error al acceder al método health: " . $e->getMessage() . "\n";
            }
            
        } catch (Exception $e) {
            echo "Error al crear instancia del controlador: " . $e->getMessage() . "\n";
        }
    } else {
        echo "NO\n";
    }
} else {
    echo "NO\n";
    echo "Posible problema: El paquete friendlypos-web no está correctamente registrado o cargado.\n";
}

echo "\nVerificando si los servicios que necesita el controlador están disponibles...\n";

echo "Verificando si OpenFacturaSDKFactory existe: ";
if (class_exists('Boctulus\OpenfacturaSdk\Factory\OpenFacturaSDKFactory')) {
    echo "SI\n";
} else {
    echo "NO\n";
    echo "ERROR: La dependencia OpenFacturaSDKFactory no está disponible.\n";
}

echo "Verificando si las variables de entorno necesarias están definidas...\n";
$openfactura_sandbox = getenv('OPENFACTURA_SANDBOX');
$openfactura_api_key_dev = getenv('OPENFACTURA_API_KEY_DEV');
$openfactura_api_key_prod = getenv('OPENFACTURA_API_KEY_PROD');

echo "OPENFACTURA_SANDBOX: " . ($openfactura_sandbox !== false ? $openfactura_sandbox : 'NO DEFINIDO') . "\n";
echo "OPENFACTURA_API_KEY_DEV: " . ($openfactura_api_key_dev !== false ? 'DEFINIDO' : 'NO DEFINIDO') . "\n";
echo "OPENFACTURA_API_KEY_PROD: " . ($openfactura_api_key_prod !== false ? 'DEFINIDO' : 'NO DEFINIDO') . "\n";

if ($openfactura_sandbox === false) {
    echo "\nAdvertencia: Las variables de entorno de OpenFactura no están definidas.\n";
    echo "Asegúrate de tenerlas en tu archivo .env:\n";
    echo "OPENFACTURA_SANDBOX=true\n";
    echo "OPENFACTURA_API_KEY_DEV=tu_api_key_de_desarrollo\n";
    echo "OPENFACTURA_API_KEY_PROD=tu_api_key_de_produccion\n";
}