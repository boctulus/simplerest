<?php
/**
 * Script de prueba usando cliente HTTP puro (sin dependencias)
 *
 * Uso:
 *   php test_raw_http.php
 */

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/FirestoreRawHTTP.php';

use Boctulus\Simplerest\modules\FirebaseTest\FirestoreRawHTTP;

echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║  FIRESTORE TEST - HTTP PURO (SIN DEPENDENCIAS, SIN gRPC)     ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

try {
    // Cargar .env
    $envFile = __DIR__ . '/../../../.env';
    echo "📁 Cargando .env desde: $envFile\n";

    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                $value = trim($value, '"\'');
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
        echo "✓ .env cargado\n\n";
    } else {
        throw new Exception("No se encontró .env en $envFile");
    }

    // Obtener credenciales
    $projectId = getenv('FIREBASE_PROJECT_ID');
    $clientEmail = getenv('FIREBASE_CLIENT_EMAIL');
    $privateKey = getenv('FIREBASE_PRIVATE_KEY');

    if (empty($projectId)) {
        throw new Exception("FIREBASE_PROJECT_ID no configurado");
    }
    if (empty($clientEmail)) {
        throw new Exception("FIREBASE_CLIENT_EMAIL no configurado");
    }
    if (empty($privateKey)) {
        throw new Exception("FIREBASE_PRIVATE_KEY no configurado");
    }

    echo "📋 Credenciales:\n";
    echo "   Project ID: $projectId\n";
    echo "   Client Email: " . substr($clientEmail, 0, 30) . "...\n";
    echo "   Private Key: " . strlen($privateKey) . " caracteres\n\n";

    // Crear cliente
    echo "🔧 Creando cliente FirestoreRawHTTP...\n\n";
    $client = new FirestoreRawHTTP($projectId, $clientEmail, $privateKey);

    echo "═══════════════════════════════════════════════════════════════\n";
    echo " TEST 1: Obtener Access Token\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";

    $token = $client->getAccessToken();
    echo "\n✓ Access token obtenido: " . substr($token, 0, 50) . "...\n\n";

    echo "═══════════════════════════════════════════════════════════════\n";
    echo " TEST 2: Crear Documento (POST)\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";

    $testData = [
        'nombre' => 'Test HTTP Puro',
        'descripcion' => 'Documento creado con peticiones HTTP directas, sin dependencias ni gRPC',
        'fecha' => date('Y-m-d H:i:s'),
        'timestamp' => time(),
        'valor' => rand(1, 1000),
        'activo' => true,
        'metodo' => 'HTTP directo con cURL',
        'version' => 'v1.0'
    ];

    echo "📝 Datos a escribir:\n";
    echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

    $result = $client->createDocument('test_raw_http', $testData);

    if ($result['success']) {
        echo "\n✅ ¡DOCUMENTO CREADO EXITOSAMENTE!\n";
        echo "   Document ID: {$result['documentId']}\n";
        echo "   Full Name: {$result['fullName']}\n\n";

        $createdDocId = $result['documentId'];

        echo "═══════════════════════════════════════════════════════════════\n";
        echo " TEST 3: Leer Documento (GET)\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        $readResult = $client->getDocument('test_raw_http', $createdDocId);

        if ($readResult['success']) {
            echo "\n✅ ¡DOCUMENTO LEÍDO EXITOSAMENTE!\n";
            echo "   Datos:\n";
            foreach ($readResult['data'] as $key => $value) {
                $valueStr = is_bool($value) ? ($value ? 'true' : 'false') : $value;
                echo "      $key: $valueStr\n";
            }
            echo "\n";
        } else {
            echo "\n❌ Error leyendo documento:\n";
            echo "   {$readResult['error']}\n";
            if (isset($readResult['message'])) {
                echo "   {$readResult['message']}\n";
            }
            echo "\n";
        }

    } else {
        echo "\n❌ ERROR AL CREAR DOCUMENTO:\n";
        echo "   {$result['error']}\n";
        if (isset($result['message'])) {
            echo "   Mensaje: {$result['message']}\n";
        }
        echo "\n";
    }

    echo "═══════════════════════════════════════════════════════════════\n";
    echo " TEST 4: Listar Documentos (GET)\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";

    $listResult = $client->listDocuments('test_raw_http', 5);

    if ($listResult['success']) {
        echo "\n✅ Documentos encontrados: {$listResult['count']}\n\n";

        foreach ($listResult['documents'] as $i => $doc) {
            echo "   Documento " . ($i + 1) . ":\n";
            echo "      ID: {$doc['id']}\n";
            echo "      Nombre: " . ($doc['data']['nombre'] ?? 'N/A') . "\n";
            echo "      Fecha: " . ($doc['data']['fecha'] ?? 'N/A') . "\n";
            echo "\n";
        }
    } else {
        echo "\n❌ Error listando documentos:\n";
        echo "   {$listResult['error']}\n";
        if (isset($listResult['message'])) {
            echo "   {$listResult['message']}\n";
        }
        echo "\n";
    }

    echo "═══════════════════════════════════════════════════════════════\n";
    echo " TEST 5: Actualizar Documento (PATCH)\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";

    if (isset($createdDocId)) {
        $updateData = [
            'nombre' => 'Test HTTP Puro - ACTUALIZADO',
            'fecha_actualizacion' => date('Y-m-d H:i:s'),
            'contador' => 999,
            'actualizado' => true
        ];

        echo "📝 Datos de actualización:\n";
        echo json_encode($updateData, JSON_PRETTY_PRINT) . "\n\n";

        $updateResult = $client->createDocument('test_raw_http', $updateData, $createdDocId);

        if ($updateResult['success']) {
            echo "\n✅ ¡DOCUMENTO ACTUALIZADO EXITOSAMENTE!\n\n";

            // Verificar lectura
            $verifyResult = $client->getDocument('test_raw_http', $createdDocId);
            if ($verifyResult['success']) {
                echo "   Datos actualizados:\n";
                foreach ($verifyResult['data'] as $key => $value) {
                    $valueStr = is_bool($value) ? ($value ? 'true' : 'false') : $value;
                    echo "      $key: $valueStr\n";
                }
                echo "\n";
            }
        } else {
            echo "\n❌ Error actualizando documento:\n";
            echo "   {$updateResult['error']}\n";
            if (isset($updateResult['message'])) {
                echo "   {$updateResult['message']}\n";
            }
            echo "\n";
        }
    } else {
        echo "⚠️  No hay documento para actualizar (la creación falló)\n\n";
    }

    echo "═══════════════════════════════════════════════════════════════\n";
    echo " RESUMEN\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";

    echo "✓ No se usó kreait/firebase-php\n";
    echo "✓ No se usó google/cloud-firestore\n";
    echo "✓ No se usó gRPC\n";
    echo "✓ Solo cURL + OpenSSL nativos de PHP\n";
    echo "✓ Peticiones HTTP REST directas a Firestore API\n\n";

    echo "📄 Log completo guardado en:\n";
    echo "   " . $client->getLogFile() . "\n\n";

    echo "╔═══════════════════════════════════════════════════════════════╗\n";
    echo "║  ✅ TEST COMPLETADO                                           ║\n";
    echo "╚═══════════════════════════════════════════════════════════════╝\n";

} catch (Exception $e) {
    echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
    echo "║  ❌ ERROR FATAL                                               ║\n";
    echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

    echo "Tipo: " . get_class($e) . "\n";
    echo "Mensaje: {$e->getMessage()}\n";
    echo "Archivo: {$e->getFile()}:{$e->getLine()}\n\n";
    echo "Stack trace:\n{$e->getTraceAsString()}\n\n";

    exit(1);
}
