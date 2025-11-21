<?php

// Prueba para verificar si la URL base o las credenciales son el problema
$api_key = '96989ee3-5c9c-4557-851c-40d292ab4319';
$secret = 'M$72tYWz$3ZJJ71';
$base_url = 'https://uat.travelapi.ai';

echo "Probando si la URL base es correcta...\n\n";

// Probar si podemos acceder a la raíz o al endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Código HTTP para URL base: $http_code\n";
if ($http_code >= 200 && $http_code < 300) {
    echo "✓ URL base accesible\n";
} else {
    echo "✗ URL base no accesible\n";
}
echo "\n";

// Probar si el endpoint /auth/login existe
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "OPTIONS"); // Probar con OPTIONS
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Código HTTP para /auth/login con OPTIONS: $http_code\n";
echo "Esto nos dice si el endpoint existe\n\n";

// Intento con un método GET simple para ver qué pasa
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); // Probar con GET
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Código HTTP para /auth/login con GET: $http_code\n";
$response_data = json_decode($response, true);
if ($response_data) {
    echo "Respuesta: " . print_r($response_data, true) . "\n";
} else {
    echo "Respuesta: $response\n";
}
echo "\n";

// Verificar si hay otros endpoints de autenticación mencionados en la documentación
echo "Probando otros posibles endpoints de autenticación...\n";

// Probar otros posibles endpoints
$endpoints = ['/login', '/api/login', '/api/auth', '/auth'];

foreach ($endpoints as $endpoint) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['key' => $api_key, 'secret' => $secret]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Endpoint: $endpoint, Código HTTP: $http_code\n";
}

echo "\nPrueba completada.\n";