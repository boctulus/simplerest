<?php

// Archivo de prueba mejorado para autenticación con API Xeni
// Basado en el mensaje de error, probemos con encabezados de fecha

$api_key = '96989ee3-5c9c-4557-851c-40d292ab4319';
$secret = 'M$72tYWz$3ZJJ71';
$base_url = 'https://uat.travelapi.ai';

echo "Probando métodos con encabezados de fecha y posibles formatos de autenticación...\n\n";

// Intentar con un encabezado Date como sugirió el error
echo "Método: Autenticación básica con encabezado Date\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['key' => $api_key, 'secret' => $secret]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . base64_encode($api_key . ':' . $secret),
    'Content-Type: application/json',
    'Date: ' . gmdate('D, d M Y H:i:s T')  // Formato de fecha RFC 2822
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

// Intentar con X-Amz-Date en lugar de Date
echo "Método: Autenticación básica con encabezado X-Amz-Date\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['key' => $api_key, 'secret' => $secret]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . base64_encode($api_key . ':' . $secret),
    'Content-Type: application/json',
    'X-Amz-Date: ' . gmdate('Ymd\THis\Z')  // Formato ISO 8601
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

// Intentar con un formato que no sea Basic
echo "Método: XeniApiKey formato personalizado\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['key' => $api_key, 'secret' => $secret]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: XeniApiKey ' . $api_key,
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

// Intentar con ambos headers: API Key y Secret como headers
echo "Método: Headers X-API-Key y X-API-Secret con contenido\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['key' => $api_key, 'secret' => $secret]));
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

echo "Pruebas completadas.\n";