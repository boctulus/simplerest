<?php

namespace Boctulus\Simplerest\modules\FirebaseTest;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Module;
use Boctulus\Simplerest\Core\Libs\Env;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Http\HttpClientOptions;

class FirebaseTest extends Module
{
    protected $firebase;
    protected $logFile;
    protected $useRestTransport = false; // Cambiar a true para forzar REST

    function __construct(){
        parent::__construct();

        // Configurar logging
        $this->logFile = __DIR__ . '/logs/firebase_debug.log';
        $this->setupErrorHandlers();

        $this->initializeFirebase();
    }

    /**
     * Configura handlers globales de errores y logging
     */
    protected function setupErrorHandlers()
    {
        // Crear directorio de logs si no existe
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Configuración de errores PHP
        error_reporting(E_ALL);
        ini_set('display_errors', '1'); // En producción usar '0' y solo log
        ini_set('log_errors', '1');
        ini_set('error_log', $this->logFile);

        // Error handler personalizado
        set_error_handler(function($severity, $message, $file, $line) {
            $this->logError("[PHP ERROR] $message in $file:$line (severity: $severity)");
            return false; // Permitir comportamiento normal después
        });

        // Shutdown function para capturar errores fatales
        register_shutdown_function(function() {
            $err = error_get_last();
            if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                $this->logError("[SHUTDOWN ERROR] " . print_r($err, true));
            }
        });
    }

    /**
     * Log helper para escribir mensajes de debug/error
     */
    protected function logError($message, $context = [])
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        $logMessage = "[$timestamp] $message$contextStr\n";

        error_log($logMessage, 3, $this->logFile);
    }

    /**
     * Inicializa la conexión con Firebase con opciones avanzadas
     */
    protected function initializeFirebase()
    {
        try {
            $this->logError("[INIT] Iniciando Firebase...");

            // Obtener credenciales desde .env
            $projectId = env('FIREBASE_PROJECT_ID');
            $clientEmail = env('FIREBASE_CLIENT_EMAIL');
            $privateKey = env('FIREBASE_PRIVATE_KEY');

            if (empty($projectId)) {
                throw new \Exception("FIREBASE_PROJECT_ID no está configurado en .env");
            }

            $this->logError("[INIT] Project ID: $projectId");

            // Verificar extensiones disponibles
            $hasGrpc = extension_loaded('grpc');
            $hasProtobuf = extension_loaded('protobuf');
            $this->logError("[INIT] Extensiones - gRPC: " . ($hasGrpc ? 'SÍ' : 'NO') . ", Protobuf: " . ($hasProtobuf ? 'SÍ' : 'NO'));

            // Configurar opciones HTTP con timeout
            $httpOptions = HttpClientOptions::default()
                ->withTimeOut(30.0)
                ->withConnectTimeout(10.0);

            // Crear la factory base
            $factory = new Factory();

            // Si tenemos credenciales completas de service account
            if (!empty($clientEmail) && !empty($privateKey)) {
                // Crear archivo temporal con credenciales JSON
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

                $tempFile = sys_get_temp_dir() . '/firebase-sa-' . md5($projectId) . '.json';
                file_put_contents($tempFile, json_encode($serviceAccountData, JSON_PRETTY_PRINT));

                $this->logError("[INIT] Archivo de credenciales temporal creado: $tempFile");

                // Validar que el JSON sea válido
                $validated = json_decode(file_get_contents($tempFile), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Error validando JSON de credenciales: " . json_last_error_msg());
                }

                $factory = $factory->withServiceAccount($tempFile);
                $this->logError("[INIT] Service Account configurado");
            } else {
                $factory = $factory->withProjectId($projectId);
                $this->logError("[INIT] Solo Project ID configurado (funcionalidad limitada)");
            }

            // Configurar cliente HTTP
            $factory = $factory->withHttpClientOptions($httpOptions);

            // Si se fuerza REST o no hay gRPC disponible, configurar transporte REST para Firestore
            if ($this->useRestTransport || !$hasGrpc) {
                $this->logError("[INIT] Usando transporte REST para Firestore");

                try {
                    $factory = $factory->withFirestoreClientConfig([
                        'transport' => 'rest',
                    ]);
                } catch (\Throwable $e) {
                    $this->logError("[INIT] No se pudo configurar REST transport: " . $e->getMessage());
                }
            } else {
                $this->logError("[INIT] Usando transporte gRPC (por defecto)");
            }

            $this->firebase = $factory;
            $this->logError("[INIT] Firebase inicializado correctamente");

            // Verificar que podemos crear el cliente Firestore
            try {
                $firestore = $this->firebase->createFirestore()->database();
                $this->logError("[INIT] Cliente Firestore creado OK");
            } catch (\Throwable $e) {
                $this->logError("[INIT] Error creando cliente Firestore: " . get_class($e) . " - " . $e->getMessage());
                $this->logError("[INIT] Trace: " . $e->getTraceAsString());
                throw $e;
            }

        } catch (\Throwable $e) {
            $this->logError("[INIT] Error fatal al inicializar Firebase: " . get_class($e) . " - " . $e->getMessage());
            echo "Error al inicializar Firebase: " . $e->getMessage() . "\n";
            $this->firebase = null;
        }
    }

    /**
     * Página de inicio con enlaces a todas las pruebas
     */
    function index()
    {
        if (!$this->firebase) {
            return "Firebase no está correctamente configurado. Verifica las credenciales en .env";
        }

        $html = '<h1>Firebase Test Module</h1>';
        $html .= '<p>Pruebas disponibles:</p>';
        $html .= '<ul>';
        $html .= '<li><a href="/firebase-test/firestore">Test Firestore (Database)</a></li>';
        $html .= '<li><a href="/firebase-test/auth">Test Authentication</a></li>';
        $html .= '<li><a href="/firebase-test/realtime-db">Test Realtime Database</a></li>';
        $html .= '<li><a href="/firebase-test/storage">Test Storage</a></li>';
        $html .= '<li><a href="/firebase-test/config">Mostrar Configuración</a></li>';
        $html .= '</ul>';

        return $html;
    }

    /**
     * Muestra la configuración actual de Firebase
     */
    function show_config()
    {
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

        $html = '<h1>Configuración de Firebase</h1>';
        $html .= '<pre>' . print_r($config, true) . '</pre>';
        $html .= '<p><a href="/firebase-test">Volver</a></p>';

        return $html;
    }

    /**
     * Prueba de Firestore (Cloud Firestore) con logging avanzado
     */
    function test_firestore()
    {
        $html = '<h2>Test Firestore</h2>';
        $writeSuccess = false;
        $writeMethod = '';
        $docId = null;

        try {
            $this->logError("[FIRESTORE] Iniciando test de Firestore...");
            $firestore = $this->firebase->createFirestore()->database();
            $this->logError("[FIRESTORE] Cliente Firestore obtenido");

            // Crear un documento
            $collection = $firestore->collection('test_collection');
            $this->logError("[FIRESTORE] Colección 'test_collection' obtenida");

            $document = $collection->newDocument();
            $docId = $document->id();
            $this->logError("[FIRESTORE] Nuevo documento creado con ID: $docId");

            $data = [
                'nombre' => 'Test Document',
                'fecha' => date('Y-m-d H:i:s'),
                'valor' => rand(1, 100),
                'activo' => true,
                'timestamp' => time(),
            ];

            $this->logError("[FIRESTORE] Datos a escribir: " . json_encode($data));

            // Intentar escribir con set() usando Throwable para capturar TODO
            try {
                $this->logError("[FIRESTORE] Intentando escribir con set()...");
                $document->set($data);
                $this->logError("[FIRESTORE] set() completado sin excepción");

                // Verificar que realmente se escribió
                $snap = $document->snapshot();
                $this->logError("[FIRESTORE] Snapshot obtenido, exists: " . ($snap->exists() ? 'YES' : 'NO'));

                if ($snap->exists()) {
                    $writeSuccess = true;
                    $writeMethod = 'set()';
                    $this->logError("[FIRESTORE] Escritura verificada OK con set()");
                    $html .= '<div style="background: #d4edda; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb;">';
                    $html .= '<strong>✓ Escritura exitosa con set()</strong>';
                    $html .= '</div>';
                } else {
                    $this->logError("[FIRESTORE] ADVERTENCIA: set() no lanzó excepción pero snapshot no existe");
                    $html .= '<div style="background: #fff3cd; padding: 10px; margin: 10px 0; border: 1px solid #ffc107;">';
                    $html .= '<strong>⚠ set() completó pero no se verificó escritura</strong>';
                    $html .= '</div>';
                }
            } catch (\Throwable $t) {
                $this->logError("[FIRESTORE] set() falló: " . get_class($t) . " - " . $t->getMessage());
                $this->logError("[FIRESTORE] Trace: " . $t->getTraceAsString());

                $html .= '<div style="background: #f8d7da; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb;">';
                $html .= '<strong>✗ Error con set():</strong> ' . get_class($t) . '<br>';
                $html .= htmlspecialchars($t->getMessage());
                $html .= '</div>';

                // Fallback: intentar con add() en lugar de set()
                try {
                    $this->logError("[FIRESTORE] Intentando fallback con add()...");
                    $addedDoc = $collection->add($data);
                    $docId = $addedDoc->id();
                    $this->logError("[FIRESTORE] add() exitoso, nuevo ID: $docId");

                    $writeSuccess = true;
                    $writeMethod = 'add() [fallback]';

                    $html .= '<div style="background: #d4edda; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb;">';
                    $html .= '<strong>✓ Fallback exitoso con add()</strong><br>';
                    $html .= 'Nuevo ID: ' . $docId;
                    $html .= '</div>';
                } catch (\Throwable $t2) {
                    $this->logError("[FIRESTORE] add() también falló: " . get_class($t2) . " - " . $t2->getMessage());
                    $this->logError("[FIRESTORE] Trace: " . $t2->getTraceAsString());

                    $html .= '<div style="background: #f8d7da; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb;">';
                    $html .= '<strong>✗ Fallback add() también falló:</strong> ' . get_class($t2) . '<br>';
                    $html .= htmlspecialchars($t2->getMessage());
                    $html .= '</div>';
                }
            }

            if ($writeSuccess) {
                $html .= '<h3>Documento escrito con ' . $writeMethod . ':</h3>';
                $html .= '<pre>' . print_r($data, true) . '</pre>';
                $html .= '<p><strong>ID:</strong> ' . $docId . '</p>';
            }

            // Leer documentos existentes
            try {
                $this->logError("[FIRESTORE] Leyendo documentos existentes...");
                $documents = $collection->limit(10)->documents();
                $html .= '<h3>Documentos en la colección (últimos 10):</h3>';

                $count = 0;
                foreach ($documents as $doc) {
                    if ($doc->exists()) {
                        $count++;
                        $html .= '<div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">';
                        $html .= '<strong>ID:</strong> ' . $doc->id() . '<br>';
                        $html .= '<pre>' . print_r($doc->data(), true) . '</pre>';
                        $html .= '</div>';
                    }
                }

                $this->logError("[FIRESTORE] Lectura completada, $count documentos encontrados");

                if ($count === 0) {
                    $html .= '<p>No hay documentos en la colección.</p>';
                }
            } catch (\Throwable $t) {
                $this->logError("[FIRESTORE] Error leyendo documentos: " . $t->getMessage());
                $html .= '<div style="background: #f8d7da; padding: 10px; margin: 10px 0;">';
                $html .= '<strong>Error leyendo documentos:</strong> ' . htmlspecialchars($t->getMessage());
                $html .= '</div>';
            }

            // Información de debug
            $html .= '<h3>Debug Info:</h3>';
            $html .= '<pre>';
            $html .= "Transporte: " . ($this->useRestTransport ? 'REST (forzado)' : 'gRPC (default)') . "\n";
            $html .= "gRPC instalado: " . (extension_loaded('grpc') ? 'SÍ' : 'NO') . "\n";
            $html .= "Protobuf instalado: " . (extension_loaded('protobuf') ? 'SÍ' : 'NO') . "\n";
            $html .= "Log file: {$this->logFile}\n";
            $html .= '</pre>';

            $html .= '<p><a href="/firebase-test">Volver</a></p>';

            return $html;

        } catch (\Throwable $e) {
            $this->logError("[FIRESTORE] Error fatal en test_firestore: " . get_class($e) . " - " . $e->getMessage());
            $this->logError("[FIRESTORE] Trace: " . $e->getTraceAsString());

            return '<h2>Error Fatal en Firestore</h2>' .
                   '<div style="background: #f8d7da; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb;">' .
                   '<strong>Tipo:</strong> ' . get_class($e) . '<br>' .
                   '<strong>Mensaje:</strong> ' . htmlspecialchars($e->getMessage()) . '<br>' .
                   '<strong>Archivo:</strong> ' . $e->getFile() . ':' . $e->getLine() . '<br>' .
                   '<strong>Log:</strong> ' . $this->logFile .
                   '</div>' .
                   '<p><a href="/firebase-test">Volver</a></p>';
        }
    }

    /**
     * Prueba de Authentication
     */
    function test_auth()
    {
        try {
            $auth = $this->firebase->createAuth();

            $html = '<h2>Test Authentication</h2>';

            // Listar usuarios (primeros 10)
            $users = $auth->listUsers($maxResults = 10);

            $html .= '<h3>Usuarios registrados (máximo 10):</h3>';

            if (iterator_count($users) === 0) {
                $html .= '<p>No hay usuarios registrados.</p>';
            } else {
                foreach ($users as $user) {
                    $html .= '<div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">';
                    $html .= '<strong>UID:</strong> ' . $user->uid . '<br>';
                    $html .= '<strong>Email:</strong> ' . ($user->email ?? 'N/A') . '<br>';
                    $html .= '<strong>Display Name:</strong> ' . ($user->displayName ?? 'N/A') . '<br>';
                    $html .= '<strong>Creado:</strong> ' . $user->metadata->createdAt->format('Y-m-d H:i:s') . '<br>';
                    $html .= '<strong>Verificado:</strong> ' . ($user->emailVerified ? 'Sí' : 'No') . '<br>';
                    $html .= '</div>';
                }
            }

            $html .= '<h3>Crear usuario de prueba:</h3>';
            $html .= '<form method="post">';
            $html .= 'Email: <input type="email" name="email" required><br><br>';
            $html .= 'Password: <input type="password" name="password" required><br><br>';
            $html .= 'Display Name: <input type="text" name="displayName"><br><br>';
            $html .= '<button type="submit" name="create_user">Crear Usuario</button>';
            $html .= '</form>';

            // Crear usuario si se envió el formulario
            if (isset($_POST['create_user'])) {
                try {
                    $userProperties = [
                        'email' => $_POST['email'],
                        'password' => $_POST['password'],
                    ];

                    if (!empty($_POST['displayName'])) {
                        $userProperties['displayName'] = $_POST['displayName'];
                    }

                    $createdUser = $auth->createUser($userProperties);

                    $html .= '<div style="background: #d4edda; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb;">';
                    $html .= '<h4>Usuario creado exitosamente!</h4>';
                    $html .= '<strong>UID:</strong> ' . $createdUser->uid . '<br>';
                    $html .= '<strong>Email:</strong> ' . $createdUser->email . '<br>';
                    $html .= '</div>';
                } catch (\Exception $e) {
                    $html .= '<div style="background: #f8d7da; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb;">';
                    $html .= '<strong>Error al crear usuario:</strong> ' . $e->getMessage();
                    $html .= '</div>';
                }
            }

            $html .= '<p><a href="/firebase-test">Volver</a></p>';

            return $html;

        } catch (\Exception $e) {
            return '<h2>Error en Authentication</h2><p>' . $e->getMessage() . '</p><p><a href="/firebase-test">Volver</a></p>';
        }
    }

    /**
     * Prueba de Realtime Database
     */
    function test_realtime_db()
    {
        try {
            $database = $this->firebase->createDatabase();

            $html = '<h2>Test Realtime Database</h2>';

            // Escribir datos
            $reference = $database->getReference('test_data');

            $data = [
                'timestamp' => time(),
                'message' => 'Test desde PHP',
                'random' => rand(1, 1000),
            ];

            $newEntry = $reference->push($data);

            $html .= '<h3>Datos escritos:</h3>';
            $html .= '<pre>' . print_r($data, true) . '</pre>';
            $html .= '<p>Key: ' . $newEntry->getKey() . '</p>';

            // Leer datos
            $snapshot = $reference->orderByKey()->limitToLast(10)->getSnapshot();
            $values = $snapshot->getValue();

            $html .= '<h3>Últimos 10 registros:</h3>';
            if ($values) {
                $html .= '<pre>' . print_r($values, true) . '</pre>';
            } else {
                $html .= '<p>No hay datos.</p>';
            }

            $html .= '<p><a href="/firebase-test">Volver</a></p>';

            return $html;

        } catch (\Exception $e) {
            return '<h2>Error en Realtime Database</h2><p>' . $e->getMessage() . '</p><p><a href="/firebase-test">Volver</a></p>';
        }
    }

    /**
     * Prueba de Storage
     */
    function test_storage()
    {
        try {
            $storage = $this->firebase->createStorage();
            $bucket = $storage->getBucket();

            $html = '<h2>Test Storage</h2>';
            $html .= '<h3>Bucket: ' . $bucket->name() . '</h3>';

            // Listar archivos
            $objects = $bucket->objects(['maxResults' => 10]);

            $html .= '<h3>Archivos en el bucket (máximo 10):</h3>';

            $fileCount = 0;
            foreach ($objects as $object) {
                $html .= '<div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">';
                $html .= '<strong>Nombre:</strong> ' . $object->name() . '<br>';
                $html .= '<strong>Tamaño:</strong> ' . number_format($object->info()['size'] / 1024, 2) . ' KB<br>';
                $html .= '<strong>Tipo:</strong> ' . ($object->info()['contentType'] ?? 'N/A') . '<br>';
                $html .= '<strong>Actualizado:</strong> ' . $object->info()['updated'] . '<br>';
                $html .= '</div>';
                $fileCount++;
            }

            if ($fileCount === 0) {
                $html .= '<p>No hay archivos en el bucket.</p>';
            }

            // Formulario para subir archivo
            $html .= '<h3>Subir archivo:</h3>';
            $html .= '<form method="post" enctype="multipart/form-data">';
            $html .= '<input type="file" name="file" required><br><br>';
            $html .= '<button type="submit" name="upload_file">Subir Archivo</button>';
            $html .= '</form>';

            // Subir archivo si se envió el formulario
            if (isset($_POST['upload_file']) && isset($_FILES['file'])) {
                try {
                    $file = $_FILES['file'];

                    if ($file['error'] === UPLOAD_ERR_OK) {
                        $fileName = 'test_uploads/' . basename($file['name']);
                        $uploadedObject = $bucket->upload(
                            fopen($file['tmp_name'], 'r'),
                            [
                                'name' => $fileName,
                                'metadata' => [
                                    'contentType' => $file['type']
                                ]
                            ]
                        );

                        $html .= '<div style="background: #d4edda; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb;">';
                        $html .= '<h4>Archivo subido exitosamente!</h4>';
                        $html .= '<strong>Nombre:</strong> ' . $uploadedObject->name() . '<br>';
                        $html .= '<strong>Tamaño:</strong> ' . number_format($file['size'] / 1024, 2) . ' KB<br>';
                        $html .= '</div>';
                    } else {
                        throw new \Exception('Error al subir el archivo');
                    }
                } catch (\Exception $e) {
                    $html .= '<div style="background: #f8d7da; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb;">';
                    $html .= '<strong>Error al subir archivo:</strong> ' . $e->getMessage();
                    $html .= '</div>';
                }
            }

            $html .= '<p><a href="/firebase-test">Volver</a></p>';

            return $html;

        } catch (\Exception $e) {
            return '<h2>Error en Storage</h2><p>' . $e->getMessage() . '</p><p><a href="/firebase-test">Volver</a></p>';
        }
    }
}