<?php

// Archivo de prueba más simple para autenticación con API Xeni
// Basado en la descripción original: enviar key y secret

$api_key = '96989ee3-5c9c-4557-851c-40d292ab4319';
$secret = 'M$72tYWz$3ZJJ71';
$base_url = 'https://uat.travelapi.ai';

echo "Probando el método más simple: solo cuerpo JSON con key y secret\n\n";

// Solo probar con el cuerpo JSON pero sin encabezado Authorization
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['key' => $api_key, 'secret' => $secret]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Código HTTP: $http_code\n";
$response_data = json_decode($response, true);
if ($response_data) {
    echo "Respuesta: " . print_r($response_data, true) . "\n";
} else {
    echo "Respuesta: $response\n";
}

echo "\n";

// Tambien probar con application/x-www-form-urlencoded
echo "Probando con formato form-url-encoded\n\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['key' => $api_key, 'secret' => $secret]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Código HTTP: $http_code\n";
$response_data = json_decode($response, true);
if ($response_data) {
    echo "Respuesta: " . print_r($response_data, true) . "\n";
} else {
    echo "Respuesta: $response\n";
}

echo "\n";

// Probar sin cuerpo pero con parámetros en URL
echo "Probando con parámetros en URL\n\n";
$params = http_build_query(['key' => $api_key, 'secret' => $secret]);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login?' . $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true); // Aún usando POST, pero con parámetros en URL
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Código HTTP: $http_code\n";
$response_data = json_decode($response, true);
if ($response_data) {
    echo "Respuesta: " . print_r($response_data, true) . "\n";
} else {
    echo "Respuesta: $response\n";
}

echo "\nPrueba completada.\n";