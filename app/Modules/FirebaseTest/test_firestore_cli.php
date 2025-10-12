<?php
/**
 * Script CLI para probar escritura en Firestore
 *
 * Uso:
 *   php test_firestore_cli.php [--rest]
 *
 * Opciones:
 *   --rest    Fuerza el uso de transporte REST en lugar de gRPC
 */

require_once __DIR__ . '/../../../vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Http\HttpClientOptions;

// Configurar logging
$logFile = __DIR__ . '/logs/firestore_cli_test.log';
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

function logMessage($message, $logFile) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    echo $logMessage;
    error_log($logMessage, 3, $logFile);
}

// Configurar error reporting completo
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', $logFile);

set_error_handler(function($severity, $message, $file, $line) use ($logFile) {
    logMessage("[PHP ERROR] $message in $file:$line (severity: $severity)", $logFile);
    return false;
});

register_shutdown_function(function() use ($logFile) {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        logMessage("[SHUTDOWN ERROR] " . print_r($err, true), $logFile);
    }
});

// Parsear argumentos
$useRest = in_array('--rest', $argv);

logMessage("=== INICIO TEST FIRESTORE CLI ===", $logFile);
logMessage("Transporte: " . ($useRest ? 'REST (forzado)' : 'gRPC (default)'), $logFile);

try {
    // Cargar .env
    $envFile = __DIR__ . '/../../../.env';
    if (file_exists($envFile)) {
        logMessage("Cargando .env desde: $envFile", $logFile);
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                // Eliminar comillas si existen
                $value = trim($value, '"\'');
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    } else {
        logMessage("ADVERTENCIA: No se encontró .env en $envFile", $logFile);
    }

    // Obtener credenciales
    $projectId = getenv('FIREBASE_PROJECT_ID');
    $clientEmail = getenv('FIREBASE_CLIENT_EMAIL');
    $privateKey = getenv('FIREBASE_PRIVATE_KEY');

    if (empty($projectId)) {
        throw new Exception("FIREBASE_PROJECT_ID no está configurado");
    }

    logMessage("Project ID: $projectId", $logFile);
    logMessage("Client Email: " . ($clientEmail ? substr($clientEmail, 0, 20) . '...' : 'NO CONFIGURADO'), $logFile);
    logMessage("Private Key: " . ($privateKey ? 'CONFIGURADO (' . strlen($privateKey) . ' chars)' : 'NO CONFIGURADO'), $logFile);

    // Verificar extensiones
    $hasGrpc = extension_loaded('grpc');
    $hasProtobuf = extension_loaded('protobuf');
    logMessage("Extensión gRPC: " . ($hasGrpc ? 'SÍ' : 'NO'), $logFile);
    logMessage("Extensión Protobuf: " . ($hasProtobuf ? 'SÍ' : 'NO'), $logFile);

    if (!$hasGrpc && !$useRest) {
        logMessage("ADVERTENCIA: gRPC no disponible y no se forzó REST. Esto puede causar problemas.", $logFile);
    }

    // Crear service account JSON
    $serviceAccountData = [
        'type' => 'service_account',
        'project_id' => $projectId,
        'client_email' => $clientEmail,
        'private_key' => str_replace('\\n', "\n", $privateKey),
        'private_key_id' => 'key-id',
        'client_id' => 'client-id',
        'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
        'token_uri' => 'https://oauth2.googleapis.com/token',
        'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
    ];

    $tempFile = sys_get_temp_dir() . '/firebase-cli-test-' . md5($projectId) . '.json';
    file_put_contents($tempFile, json_encode($serviceAccountData, JSON_PRETTY_PRINT));
    logMessage("Archivo temporal de credenciales: $tempFile", $logFile);

    // Validar JSON
    $validated = json_decode(file_get_contents($tempFile), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON de credenciales inválido: " . json_last_error_msg());
    }
    logMessage("Credenciales JSON validadas OK", $logFile);

    // Configurar Factory
    $httpOptions = HttpClientOptions::default()
        ->withTimeOut(30.0)
        ->withConnectTimeout(10.0);

    $factory = (new Factory())
        ->withServiceAccount($tempFile)
        ->withHttpClientOptions($httpOptions);

    // Configurar transporte si se solicita REST
    if ($useRest || !$hasGrpc) {
        logMessage("Configurando transporte REST...", $logFile);
        try {
            $factory = $factory->withFirestoreClientConfig([
                'transport' => 'rest',
            ]);
            logMessage("Transporte REST configurado", $logFile);
        } catch (Throwable $e) {
            logMessage("No se pudo configurar REST: " . $e->getMessage(), $logFile);
        }
    }

    // Crear cliente Firestore
    logMessage("Creando cliente Firestore...", $logFile);
    $firestore = $factory->createFirestore()->database();
    logMessage("Cliente Firestore creado OK", $logFile);

    // Obtener colección
    $collection = $firestore->collection('test_collection_cli');
    logMessage("Colección obtenida: test_collection_cli", $logFile);

    // Preparar datos de prueba
    $data = [
        'nombre' => 'CLI Test Document',
        'fecha' => date('Y-m-d H:i:s'),
        'valor' => rand(1, 1000),
        'activo' => true,
        'timestamp' => time(),
        'test_type' => 'cli_script',
        'transport' => $useRest ? 'rest' : 'grpc',
    ];
    logMessage("Datos a escribir: " . json_encode($data), $logFile);

    // Prueba 1: Intentar con set()
    logMessage("\n--- PRUEBA 1: set() ---", $logFile);
    $document = $collection->newDocument();
    $docId = $document->id();
    logMessage("Documento creado con ID: $docId", $logFile);

    try {
        logMessage("Ejecutando set()...", $logFile);
        $document->set($data);
        logMessage("set() completado sin excepción", $logFile);

        // Verificar con snapshot
        $snap = $document->snapshot();
        logMessage("Snapshot obtenido, exists: " . ($snap->exists() ? 'YES' : 'NO'), $logFile);

        if ($snap->exists()) {
            logMessage("✓ SUCCESS: Documento escrito y verificado con set()", $logFile);
            $readData = $snap->data();
            logMessage("Datos leídos: " . json_encode($readData), $logFile);
        } else {
            logMessage("✗ FALLO: set() no lanzó error pero snapshot no existe", $logFile);
        }
    } catch (Throwable $t) {
        logMessage("✗ EXCEPCIÓN en set(): " . get_class($t), $logFile);
        logMessage("Mensaje: " . $t->getMessage(), $logFile);
        logMessage("Archivo: " . $t->getFile() . ":" . $t->getLine(), $logFile);
        logMessage("Stack trace:\n" . $t->getTraceAsString(), $logFile);
    }

    // Prueba 2: Intentar con add()
    logMessage("\n--- PRUEBA 2: add() ---", $logFile);
    try {
        logMessage("Ejecutando add()...", $logFile);
        $addedDoc = $collection->add($data);
        $addedId = $addedDoc->id();
        logMessage("✓ SUCCESS: Documento creado con add(), ID: $addedId", $logFile);

        // Verificar lectura
        $snap = $addedDoc->snapshot();
        if ($snap->exists()) {
            logMessage("✓ Documento verificado con snapshot", $logFile);
        } else {
            logMessage("✗ ADVERTENCIA: add() OK pero snapshot no existe", $logFile);
        }
    } catch (Throwable $t) {
        logMessage("✗ EXCEPCIÓN en add(): " . get_class($t), $logFile);
        logMessage("Mensaje: " . $t->getMessage(), $logFile);
        logMessage("Stack trace:\n" . $t->getTraceAsString(), $logFile);
    }

    // Prueba 3: Leer documentos existentes
    logMessage("\n--- PRUEBA 3: Lectura de documentos ---", $logFile);
    try {
        $documents = $collection->limit(5)->documents();
        $count = 0;
        foreach ($documents as $doc) {
            if ($doc->exists()) {
                $count++;
                logMessage("Documento $count - ID: " . $doc->id(), $logFile);
            }
        }
        logMessage("✓ Total documentos leídos: $count", $logFile);
    } catch (Throwable $t) {
        logMessage("✗ Error leyendo documentos: " . $t->getMessage(), $logFile);
    }

    logMessage("\n=== FIN TEST - Revisa $logFile para detalles completos ===", $logFile);

} catch (Throwable $e) {
    logMessage("\n=== ERROR FATAL ===", $logFile);
    logMessage("Tipo: " . get_class($e), $logFile);
    logMessage("Mensaje: " . $e->getMessage(), $logFile);
    logMessage("Archivo: " . $e->getFile() . ":" . $e->getLine(), $logFile);
    logMessage("Stack trace:\n" . $e->getTraceAsString(), $logFile);
    exit(1);
}
