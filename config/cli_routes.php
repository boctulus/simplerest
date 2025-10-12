<?php

use Boctulus\Simplerest\Core\CliRouter;
use Boctulus\Simplerest\Modules\FirebaseTest\FirebaseTest;

// Inicializar el router
$route = CliRouter::getInstance();


// funciones anonimas sin parametros --ok
CliRouter::command('version', function() {
    return 'SimpleRest Framework v1.0.0';
});

// funciones anonimas con parametros --ok
CliRouter::command('pow', function($num, $exp) {
    return pow($num, $exp);
});

// Comandos con controladores
CliRouter::command('dbdriver', 'Boctulus\Simplerest\Controllers\DumbController@db_driver');

// Comandos con controladores -- en este caso se pasan parametros sin validacion
CliRouter::command('plus_1', 'Boctulus\Simplerest\Controllers\DumbController@inc2');

// mas... aun sin soporte

// // Comandos con controladores -- en este caso se pasan parametros con validacion
// CliRouter::command('increment/{num}', 'Boctulus\Simplerest\Controllers\folder\SomeController@inc2')
// ->where(['num' => '[0-9]+']);

// // Comandos con parámetros y restricciones
// CliRouter::command('migrations {action}', 'MigrationsController@handle')
//     ->where(['action' => 'migrate|rollback|status|reset']);

// // Comandos con nombres descriptivos
// CliRouter::command('route:list', 'RouteController@listRoutes')
//     ->name('routes.list');

// //Comandos con alias
// CliRouter::command('serve', function() {
//     $port = 8000;
//     $host = 'localhost';
//     System::execAtRoot("php -S $host:$port -t public");
//     return "Server started at http://$host:$port";
// })->alias('server');

// // Comandos con subcomandos agrupados
// CliRouter::group('db', function() {
//     CliRouter::command('backup', 'DatabaseController@backup');
//     CliRouter::command('restore {file}', 'DatabaseController@restore')
//         ->where(['file' => '.+\.sql']);
//     CliRouter::command('optimize', 'DatabaseController@optimize');
// });

// // Comandos con argumentos variables
// CliRouter::command('test {file?}', function($file = null) {
//     if ($file) {
//         return System::execAtRoot("phpunit $file");
//     }
//     return System::execAtRoot("phpunit");
// });

// // Comandos con opciones
// CliRouter::command('env:set {key} {value}', function($key, $value) {
//     // Actualizar el archivo .env
//     $envFile = ROOT_PATH . '.env';
//     $content = file_get_contents($envFile);
//     $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
//     file_put_contents($envFile, $content);
//     return "Environment variable {$key} set to {$value}";
// });

// Firebase Test CLI Commands
CliRouter::command('firebase:config', function() {
    $module = new FirebaseTest();
    $config = [
        'FIREBASE_API_KEY' => env('FIREBASE_API_KEY') ? 'Configurado' : 'No configurado',
        'FIREBASE_AUTH_DOMAIN' => env('FIREBASE_AUTH_DOMAIN'),
        'FIREBASE_PROJECT_ID' => env('FIREBASE_PROJECT_ID'),
        'FIREBASE_STORAGE_BUCKET' => env('FIREBASE_STORAGE_BUCKET'),
        'FIREBASE_MESSAGING_SENDER_ID' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'FIREBASE_APP_ID' => env('FIREBASE_APP_ID'),
        'FIREBASE_CLIENT_EMAIL' => env('FIREBASE_CLIENT_EMAIL') ? 'Configurado' : 'No configurado',
        'FIREBASE_PRIVATE_KEY' => env('FIREBASE_PRIVATE_KEY') ? 'Configurado' : 'No configurado',
    ];
    return print_r($config, true);
});

CliRouter::command('firebase:test-firestore', function() {
    $module = new FirebaseTest();
    try {
        $firebase = (new \Kreait\Firebase\Factory)->withProjectId(env('FIREBASE_PROJECT_ID'));
        $firestore = $firebase->createFirestore()->database();

        // Crear un documento de prueba
        $collection = $firestore->collection('cli_test');
        $document = $collection->newDocument();

        $data = [
            'timestamp' => time(),
            'message' => 'Test desde CLI',
            'random' => rand(1, 1000),
        ];

        $document->set($data);

        return "✓ Firestore test OK - Documento creado con ID: " . $document->id();
    } catch (\Exception $e) {
        return "✗ Error: " . $e->getMessage();
    }
});

CliRouter::command('firebase:test-realtime-db', function() {
    try {
        $firebase = (new \Kreait\Firebase\Factory)->withProjectId(env('FIREBASE_PROJECT_ID'));
        $database = $firebase->createDatabase();

        $reference = $database->getReference('cli_test');

        $data = [
            'timestamp' => time(),
            'message' => 'Test desde CLI',
            'random' => rand(1, 1000),
        ];

        $newEntry = $reference->push($data);

        return "✓ Realtime Database test OK - Key: " . $newEntry->getKey();
    } catch (\Exception $e) {
        return "✗ Error: " . $e->getMessage();
    }
});

CliRouter::command('firebase:list-users', function() {
    try {
        $firebase = (new \Kreait\Firebase\Factory)->withProjectId(env('FIREBASE_PROJECT_ID'));
        $auth = $firebase->createAuth();

        $users = $auth->listUsers($maxResults = 10);

        $output = "Firebase Users:\n\n";
        $count = 0;

        foreach ($users as $user) {
            $count++;
            $output .= "[$count] UID: {$user->uid}\n";
            $output .= "    Email: " . ($user->email ?? 'N/A') . "\n";
            $output .= "    Display Name: " . ($user->displayName ?? 'N/A') . "\n";
            $output .= "    Verified: " . ($user->emailVerified ? 'Yes' : 'No') . "\n\n";
        }

        if ($count === 0) {
            return "No hay usuarios registrados.";
        }

        return $output;
    } catch (\Exception $e) {
        return "✗ Error: " . $e->getMessage();
    }
});

CliRouter::command('firebase:test-all', function() {
    $output = "=== Firebase Complete Test ===\n\n";

    // Test 1: Config
    $output .= "1. Configuration Check:\n";
    $projectId = env('FIREBASE_PROJECT_ID');
    $output .= $projectId ? "   ✓ Project ID: $projectId\n" : "   ✗ Project ID not configured\n";

    // Test 2: Firestore
    $output .= "\n2. Testing Firestore...\n";
    try {
        $firebase = (new \Kreait\Firebase\Factory)->withProjectId(env('FIREBASE_PROJECT_ID'));
        $firestore = $firebase->createFirestore()->database();
        $collection = $firestore->collection('cli_test');
        $document = $collection->newDocument();
        $document->set(['test' => time()]);
        $output .= "   ✓ Firestore OK\n";
    } catch (\Exception $e) {
        $output .= "   ✗ Firestore Error: " . $e->getMessage() . "\n";
    }

    // Test 3: Realtime Database\n";
    try {
        $firebase = (new \Kreait\Firebase\Factory)->withProjectId(env('FIREBASE_PROJECT_ID'));
        $database = $firebase->createDatabase();
        $reference = $database->getReference('cli_test');
        $reference->push(['test' => time()]);
        $output .= "   ✓ Realtime Database OK\n";
    } catch (\Exception $e) {
        $output .= "   ✗ Realtime Database Error: " . $e->getMessage() . "\n";
    }

    // Test 4: Authentication\n";
    try {
        $firebase = (new \Kreait\Firebase\Factory)->withProjectId(env('FIREBASE_PROJECT_ID'));
        $auth = $firebase->createAuth();
        $users = $auth->listUsers($maxResults = 1);
        $output .= "   ✓ Authentication OK\n";
    } catch (\Exception $e) {
        $output .= "   ✗ Authentication Error: " . $e->getMessage() . "\n";
    }

    $output .= "\n=== Test Complete ===\n";

    return $output;
});

// Compilar todas las rutas CLI
CliRouter::compile();