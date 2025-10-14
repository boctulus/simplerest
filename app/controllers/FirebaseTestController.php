<?php

namespace Boctulus\Simplerest\Controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Kreait\Firebase\Factory;

class FirebaseTestController extends Controller
{
    function __construct() { parent::__construct(); }

    function test()
    {
        $serviceAccountPath = ETC_PATH . 'firebase_test.json';
        $bd_uri             = 'https://firestorecrud-7000.firebaseio.com';

        if (!file_exists($serviceAccountPath)) {
            dd("No existe el archivo de credenciales de Firebase en la ruta: $serviceAccountPath");
        }

        if (!is_readable($serviceAccountPath)) {
            dd("No se puede leer el archivo de credenciales de Firebase en la ruta: $serviceAccountPath");
        }        

        $factory = (new Factory())
        ->withServiceAccount($serviceAccountPath)
        ->withDatabaseUri($bd_uri);

        $auth = $factory->createAuth();
        $realtimeDatabase = $factory->createDatabase();
        $cloudMessaging = $factory->createMessaging();
        $remoteConfig = $factory->createRemoteConfig();
        $cloudStorage = $factory->createStorage();
        $firestore = $factory->createFirestore();
    }
}

