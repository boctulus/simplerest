<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Logger;
use Boctulus\Simplerest\Core\Libs\Strings;
use Kreait\Firebase\Factory;
use Google\Cloud\Core\Timestamp;
use Google\Cloud\Firestore\FirestoreClient;

class FriendlyPOSController extends Controller
{
    function __construct() { parent::__construct(); }

    function firebase_bootstrap()
    {
        $serviceAccountPath = ETC_PATH . 'firebase_test.json';
        $bd_uri             = 'https://firestorecrud-7000.firebaseio.com';

        if (!file_exists($serviceAccountPath)) {
            dd("No existe el archivo de credenciales de Firebase en la ruta: $serviceAccountPath");
        }

        if (!is_readable($serviceAccountPath)) {
            dd("No se puede leer el archivo de credenciales de Firebase en la ruta: $serviceAccountPath");
        }        

        // Cargar el contenido del archivo para verificar
        $credentials = json_decode(file_get_contents($serviceAccountPath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Error al decodificar el archivo JSON: " . json_last_error_msg());
        }

        // Usar createFirestore directamente en lugar de Factory para diagnóstico
        $firestore = new \Google\Cloud\Firestore\FirestoreClient([
            'keyFilePath' => $serviceAccountPath,
            'projectId' => $credentials['project_id'] ?? null,
        ]);

        return $firestore;
    }

    function firebase_db()
    {
        return $this->firebase_bootstrap();
    }

    function test(){
        try {
            // Ruta a tu archivo de credenciales
            $serviceAccountPath = ETC_PATH . 'firebase_test.json';
            
            echo "Intentando conectar a Firestore...\n";
            
            $firestore = new FirestoreClient([
                'keyFilePath' => $serviceAccountPath,
            ]);
            
            echo "Conexión establecida. Intentando listar colecciones...\n";
            
            $collections = $firestore->collections();
            
            echo "Colecciones disponibles:\n";
            foreach ($collections as $collection) {
                echo "- " . $collection->id() . "\n";
            }
            
            echo "Operación completada con éxito.\n";
            
        } catch (\Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
            echo "TRACE: " . $e->getTraceAsString() . "\n";
        }
    }

    function firebase_test()
    {
        putenv('GRPC_VERBOSITY=DEBUG');
        putenv('GRPC_TRACE=all');

        try {            
            $database = $this->firebase_db();

            /*
                object(Google\Cloud\Firestore\FirestoreClient)#179 (4) { ["connection":"Google\Cloud\Firestore\FirestoreClient":private]=> object(Google\Cloud\Firestore\Connection\Grpc)#182 (5) { ["serializer"]=> string(25) "Google\ApiCore\Serializer" ["firestore"]=> string(41) "Google\Cloud\Firestore\V1\FirestoreClient" ["resourcePrefixHeader"]=> string(47) "projects/firestorecrud-7000/databases/(default)" ["databaseRoutingHeader"]=> string(51) "project_id=firestorecrud-7000&database_id=(default)" ["isUsingEmulator"]=> bool(false) } ["database":"Google\Cloud\Firestore\FirestoreClient":private]=> string(9) "(default)" ["valueMapper":"Google\Cloud\Firestore\FirestoreClient":private]=> object(Google\Cloud\Firestore\ValueMapper)#842 (2) { ["connection"]=> string(38) "Google\Cloud\Firestore\Connection\Grpc" ["returnInt64AsObject"]=> bool(false) } ["projectId":"Google\Cloud\Firestore\FirestoreClient":private]=> string(18) "firestorecrud-7000" }
            */
            dd($database, 'Database'); // Base de datos de Firestore      
            
            $collection = $documentReference = $database->collection('test_collection');
            
            /*
                object(Google\Cloud\Firestore\CollectionReference)#799 (4) { ["connection"]=> string(38) "Google\Cloud\Firestore\Connection\Grpc" ["valueMapper"]=> object(Google\Cloud\Firestore\ValueMapper)#842 (2) { ["connection"]=> string(38) "Google\Cloud\Firestore\Connection\Grpc" ["returnInt64AsObject"]=> bool(false) } ["name"]=> string(73) "projects/firestorecrud-7000/databases/(default)/documents/test_collection" ["parent"]=> NULL }
            */
            dd($collection, 'Collection'); // Colección de Firestore
            
            $collection->add([
                'name' => 'John Doe',
                'email' => '',
                'age' => 30,
                // 'created_at' => new Timestamp(new \DateTime()),
            ]);

            dd($documentReference->id(), 'Document ID'); // ID del documento creado

            $documents = $collection->documents();
            foreach ($documents as $document) {
                dd("Documento encontrado: " . $document->id());
            }

            dd("END.");

        } catch (\Throwable $e) {
            Logger::log($e->getMessage());
            Logger::log($e->getTraceAsString());
        }
    }

}

