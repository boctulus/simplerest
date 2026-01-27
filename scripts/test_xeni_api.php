<?php

// Archivo de prueba para autenticación con API Xeni
// Probando diferentes métodos directamente con cURL

$api_key = '96989ee3-5c9c-4557-851c-40d292ab4319';
$secret = 'M$72tYWz$3ZJJ71';
$base_url = 'https://uat.travelapi.ai';

echo "Probando diferentes métodos de autenticación con Xeni API...\n\n";

// Método 1: Credenciales en el cuerpo como JSON
echo "Método 1: Credenciales en el cuerpo como JSON\n";
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

$response_data = json_decode($response, true);
echo "Código HTTP: $http_code\n";
if ($response_data) {
    echo "Respuesta: " . print_r($response_data, true) . "\n";
} else {
    echo "Respuesta: $response\n";
}
echo "\n";

// Método 2: Credenciales como encabezados
echo "Método 2: Credenciales como encabezados X-API-Key y X-API-Secret\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([])); // Cuerpo vacío
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-Key: ' . $api_key,
    'X-API-Secret: ' . $secret,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$response_data = json_decode($response, true);
echo "Código HTTP: $http_code\n";
if ($response_data) {
    echo "Respuesta: " . print_r($response_data, true) . "\n";
} else {
    echo "Respuesta: $response\n";
}
echo "\n";

// Método 3: Autenticación básica
echo "Método 3: Autenticación básica HTTP\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . base64_encode($api_key . ':' . $secret),
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$response_data = json_decode($response, true);
echo "Código HTTP: $http_code\n";
if ($response_data) {
    echo "Respuesta: " . print_r($response_data, true) . "\n";
} else {
    echo "Respuesta: $response\n";
}
echo "\n";

// Método 4: API Key en encabezado Authorization
echo "Método 4: API Key en encabezado Authorization\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: ApiKey ' . $api_key,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$response_data = json_decode($response, true);
echo "Código HTTP: $http_code\n";
if ($response_data) {
    echo "Respuesta: " . print_r($response_data, true) . "\n";
} else {
    echo "Respuesta: $response\n";
}
echo "\n";

// Método 5: Con parámetros de consulta
echo "Método 5: Parámetros de consulta\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login?key=' . urlencode($api_key) . '&secret=' . urlencode($secret));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$response_data = json_decode($response, true);
echo "Código HTTP: $http_code\n";
if ($response_data) {
    echo "Respuesta: " . print_r($response_data, true) . "\n";
} else {
    echo "Respuesta: $response\n";
}
echo "\n";

echo "Pruebas completadas.\n";