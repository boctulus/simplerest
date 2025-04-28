<?php

namespace Boctulus\Simplerest\controllers;

use Boctulus\Simplerest\Core\Controllers\Controller;
use Kreait\Firebase\Factory;

class FirebaseTestController extends Controller
{
    function __construct() { parent::__construct(); }

    function test()
    {
        $credential_path = ETC_PATH . 'firebase_test.json';

        if (!file_exists($credential_path)) {
            dd("No existe el archivo de credenciales de Firebase en la ruta: $credential_path");
        }
        
        if (!is_readable($credential_path)) {
            dd("No se puede leer el archivo de credenciales de Firebase en la ruta: $credential_path");
        }        

        $factory = (new Factory())
        ->withServiceAccount($credential_path)
        ->withDatabaseUri('https://firestorecrud-7000.firebaseio.com');

        $auth = $factory->createAuth();
        $realtimeDatabase = $factory->createDatabase();
        $cloudMessaging = $factory->createMessaging();
        $remoteConfig = $factory->createRemoteConfig();
        $cloudStorage = $factory->createStorage();
        $firestore = $factory->createFirestore();
    }
}

