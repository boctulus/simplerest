<?php
/**
 * Script para verificar las credenciales de Firebase configuradas en .env
 *
 * Uso:
 *   php verify_credentials.php
 */

echo "=== VERIFICACIÓN DE CREDENCIALES FIREBASE ===\n\n";

// Intentar cargar .env
$envFile = __DIR__ . '/../../../.env';
echo "Buscando archivo .env en: $envFile\n";

if (!file_exists($envFile)) {
    echo "❌ ERROR: No se encuentra el archivo .env\n";
    exit(1);
}

echo "✅ Archivo .env encontrado\n\n";

// Cargar variables
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$envVars = [];

foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $value = trim($value, '"\'');
        $envVars[$key] = $value;
        putenv("$key=$value");
    }
}

echo "Variables cargadas: " . count($envVars) . "\n\n";

// Variables requeridas
$required = [
    'FIREBASE_PROJECT_ID',
    'FIREBASE_CLIENT_EMAIL',
    'FIREBASE_PRIVATE_KEY',
];

echo "--- VERIFICACIÓN DE VARIABLES REQUERIDAS ---\n\n";

$allOk = true;
foreach ($required as $var) {
    $value = getenv($var);
    $exists = !empty($value);

    if ($exists) {
        echo "✅ $var: ";
        if ($var === 'FIREBASE_PRIVATE_KEY') {
            $length = strlen($value);
            echo "CONFIGURADO ($length caracteres)\n";

            // Verificar formato
            if (strpos($value, '-----BEGIN PRIVATE KEY-----') !== false) {
                echo "   ℹ️  Formato: PEM completo (correcto)\n";
            } elseif (strpos($value, '\\n') !== false) {
                echo "   ℹ️  Formato: Con \\n literales (se convertirá a saltos de línea)\n";
            } elseif (strpos($value, "\n") !== false) {
                echo "   ⚠️  Formato: Con saltos de línea reales (puede causar problemas en .env)\n";
            } else {
                echo "   ⚠️  Formato: Sin saltos de línea visibles (verificar formato)\n";
            }

            // Verificar que contenga las líneas típicas de una private key
            $hasBegin = stripos($value, 'BEGIN PRIVATE KEY') !== false;
            $hasEnd = stripos($value, 'END PRIVATE KEY') !== false;

            if ($hasBegin && $hasEnd) {
                echo "   ✅ Estructura: BEGIN/END tags encontrados\n";
            } else {
                echo "   ❌ Estructura: Falta BEGIN o END tag\n";
                $allOk = false;
            }
        } elseif ($var === 'FIREBASE_CLIENT_EMAIL') {
            echo substr($value, 0, 30) . "...\n";
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                echo "   ✅ Formato: Email válido\n";
            } else {
                echo "   ❌ Formato: No parece un email válido\n";
                $allOk = false;
            }
        } else {
            echo "$value\n";
        }
    } else {
        echo "❌ $var: NO CONFIGURADO\n";
        $allOk = false;
    }
    echo "\n";
}

// Verificar extensiones PHP
echo "--- VERIFICACIÓN DE EXTENSIONES PHP ---\n\n";

$extensions = ['grpc', 'protobuf', 'json', 'curl', 'mbstring'];
foreach ($extensions as $ext) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? '✅' : ($ext === 'grpc' || $ext === 'protobuf' ? '⚠️ ' : '❌');
    echo "$status $ext: " . ($loaded ? 'INSTALADO' : 'NO INSTALADO') . "\n";

    if ($ext === 'grpc' && !$loaded) {
        echo "   ℹ️  gRPC es opcional pero recomendado. Usa REST transport como alternativa.\n";
    }
    if ($ext === 'protobuf' && !$loaded) {
        echo "   ℹ️  Protobuf mejora performance pero no es requerido.\n";
    }
}

echo "\n--- VERIFICACIÓN DE PAQUETES COMPOSER ---\n\n";

$composerLock = __DIR__ . '/../../../composer.lock';
if (file_exists($composerLock)) {
    $lockData = json_decode(file_get_contents($composerLock), true);
    $packages = $lockData['packages'] ?? [];

    $required = ['kreait/firebase-php', 'google/cloud-firestore'];
    foreach ($required as $pkg) {
        $found = false;
        foreach ($packages as $package) {
            if ($package['name'] === $pkg) {
                echo "✅ $pkg: " . $package['version'] . "\n";
                $found = true;
                break;
            }
        }
        if (!$found) {
            echo "❌ $pkg: NO INSTALADO\n";
            $allOk = false;
        }
    }
} else {
    echo "⚠️  composer.lock no encontrado\n";
}

echo "\n--- PRUEBA DE CREACIÓN DE JSON TEMPORAL ---\n\n";

try {
    $projectId = getenv('FIREBASE_PROJECT_ID');
    $clientEmail = getenv('FIREBASE_CLIENT_EMAIL');
    $privateKey = getenv('FIREBASE_PRIVATE_KEY');

    if (empty($projectId) || empty($clientEmail) || empty($privateKey)) {
        throw new Exception("Faltan credenciales");
    }

    $serviceAccountData = [
        'type' => 'service_account',
        'project_id' => $projectId,
        'client_email' => $clientEmail,
        'private_key' => str_replace('\\n', "\n", $privateKey),
        'private_key_id' => 'test-key-id',
        'client_id' => 'test-client-id',
        'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
        'token_uri' => 'https://oauth2.googleapis.com/token',
        'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
    ];

    $tempFile = sys_get_temp_dir() . '/firebase-verify-test.json';
    file_put_contents($tempFile, json_encode($serviceAccountData, JSON_PRETTY_PRINT));

    echo "✅ Archivo JSON temporal creado: $tempFile\n";
    echo "   Tamaño: " . filesize($tempFile) . " bytes\n";

    // Validar que sea JSON válido
    $validated = json_decode(file_get_contents($tempFile), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON inválido: " . json_last_error_msg());
    }

    echo "✅ JSON validado correctamente\n";
    echo "   Keys presentes: " . implode(', ', array_keys($validated)) . "\n";

    // Verificar estructura de private_key
    $pk = $validated['private_key'];
    if (strpos($pk, "\n") !== false) {
        echo "✅ Private key contiene saltos de línea reales (correcto)\n";
    } else {
        echo "❌ Private key no contiene saltos de línea\n";
        $allOk = false;
    }

    // Limpiar archivo temporal
    unlink($tempFile);
    echo "✅ Archivo temporal eliminado\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    $allOk = false;
}

echo "\n=== RESUMEN ===\n\n";

if ($allOk) {
    echo "✅ ✅ ✅ TODAS LAS VERIFICACIONES PASARON ✅ ✅ ✅\n";
    echo "\nPuedes proceder a ejecutar:\n";
    echo "  php test_firestore_cli.php\n";
    echo "O acceder a la interfaz web en /firebase-test\n";
} else {
    echo "❌ HAY PROBLEMAS QUE DEBEN SER CORREGIDOS\n";
    echo "\nRevisa los errores marcados con ❌ arriba.\n";
    exit(1);
}
