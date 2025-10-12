<?php

namespace Boctulus\Simplerest\Modules\FirebaseTest;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Module;
use Boctulus\Simplerest\Core\Libs\Env;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseTest extends Module
{
    protected $firebase;

    function __construct(){
        parent::__construct();
        $this->initializeFirebase();
    }

    /**
     * Inicializa la conexión con Firebase
     */
    protected function initializeFirebase()
    {
        try {
            // Método 1: Usando Service Account credentials
            // Si tienes un archivo JSON de credenciales
            // $this->firebase = (new Factory)->withServiceAccount(__DIR__ . '/config/firebase-credentials.json');

            // Método 2: Usando credenciales desde .env
            $projectId = env('FIREBASE_PROJECT_ID');
            $clientEmail = env('FIREBASE_CLIENT_EMAIL');
            $privateKey = env('FIREBASE_PRIVATE_KEY');

            if (empty($projectId)) {
                throw new \Exception("FIREBASE_PROJECT_ID no está configurado en .env");
            }

            // Si tenemos credenciales completas de service account
            if (!empty($clientEmail) && !empty($privateKey)) {
                $serviceAccount = ServiceAccount::fromValue([
                    'type' => 'service_account',
                    'project_id' => $projectId,
                    'client_email' => $clientEmail,
                    'private_key' => str_replace('\\n', "\n", $privateKey),
                ]);

                $this->firebase = (new Factory)
                    ->withServiceAccount($serviceAccount);
            } else {
                // Inicializar solo con Project ID (funcionalidad limitada)
                $this->firebase = (new Factory)
                    ->withProjectId($projectId);
            }

        } catch (\Exception $e) {
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
     * Prueba de Firestore (Cloud Firestore)
     */
    function test_firestore()
    {
        // Firestore requiere credenciales completas de Service Account
        if (empty(env('FIREBASE_CLIENT_EMAIL')) || empty(env('FIREBASE_PRIVATE_KEY'))) {
            return '<h2>Error en Firestore</h2>' .
                   '<p>Firestore requiere credenciales completas de Service Account.</p>' .
                   '<p>Por favor configura en tu archivo .env:</p>' .
                   '<ul>' .
                   '<li><code>FIREBASE_CLIENT_EMAIL</code></li>' .
                   '<li><code>FIREBASE_PRIVATE_KEY</code></li>' .
                   '</ul>' .
                   '<p>Estas credenciales las puedes obtener del archivo JSON de Service Account de Firebase.</p>' .
                   '<p><a href="/firebase-test">Volver</a></p>';
        }

        try {
            $firestore = $this->firebase->createFirestore()->database();

            $html = '<h2>Test Firestore</h2>';

            // Crear un documento
            $collection = $firestore->collection('test_collection');
            $document = $collection->newDocument();

            $data = [
                'nombre' => 'Test Document',
                'fecha' => date('Y-m-d H:i:s'),
                'valor' => rand(1, 100),
                'activo' => true
            ];

            $document->set($data);
            $html .= '<h3>Documento creado:</h3>';
            $html .= '<pre>' . print_r($data, true) . '</pre>';
            $html .= '<p>ID: ' . $document->id() . '</p>';

            // Leer documentos
            $documents = $collection->limit(10)->documents();
            $html .= '<h3>Documentos en la colección (últimos 10):</h3>';

            foreach ($documents as $doc) {
                if ($doc->exists()) {
                    $html .= '<div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">';
                    $html .= '<strong>ID:</strong> ' . $doc->id() . '<br>';
                    $html .= '<pre>' . print_r($doc->data(), true) . '</pre>';
                    $html .= '</div>';
                }
            }

            $html .= '<p><a href="/firebase-test">Volver</a></p>';

            return $html;

        } catch (\Exception $e) {
            return '<h2>Error en Firestore</h2><p>' . $e->getMessage() . '</p><p><a href="/firebase-test">Volver</a></p>';
        }
    }

    /**
     * Prueba de Authentication
     */
    function test_auth()
    {
        // Authentication requiere credenciales completas de Service Account
        if (empty(env('FIREBASE_CLIENT_EMAIL')) || empty(env('FIREBASE_PRIVATE_KEY'))) {
            return '<h2>Error en Authentication</h2>' .
                   '<p>Authentication requiere credenciales completas de Service Account.</p>' .
                   '<p>Por favor configura en tu archivo .env:</p>' .
                   '<ul>' .
                   '<li><code>FIREBASE_CLIENT_EMAIL</code></li>' .
                   '<li><code>FIREBASE_PRIVATE_KEY</code></li>' .
                   '</ul>' .
                   '<p><a href="/firebase-test">Volver</a></p>';
        }

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
        // Storage requiere credenciales completas de Service Account
        if (empty(env('FIREBASE_CLIENT_EMAIL')) || empty(env('FIREBASE_PRIVATE_KEY'))) {
            return '<h2>Error en Storage</h2>' .
                   '<p>Storage requiere credenciales completas de Service Account.</p>' .
                   '<p>Por favor configura en tu archivo .env:</p>' .
                   '<ul>' .
                   '<li><code>FIREBASE_CLIENT_EMAIL</code></li>' .
                   '<li><code>FIREBASE_PRIVATE_KEY</code></li>' .
                   '</ul>' .
                   '<p><a href="/firebase-test">Volver</a></p>';
        }

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
