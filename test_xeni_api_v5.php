<?php

// Intento de autenticación tipo AWS Signature v4 para Xeni API
// Basado en el mensaje de error que menciona los parámetros de firma

$api_key = '96989ee3-5c9c-4557-851c-40d292ab4319';
$secret = 'M$72tYWz$3ZJJ71';
$base_url = 'https://uat.travelapi.ai';

echo "Probando autenticación tipo AWS Signature (v4) con Xeni API\n\n";

// Intentar crear una firma tipo AWS
// Aunque no es exactamente AWS, el mensaje de error sugiere un formato similar
$date = gmdate('Ymd\THis\Z');  // Formato ISO 8601
$date_header = gmdate('D, d M Y H:i:s T');  // Formato RFC 2822

// Construir el encabezado de autorización como AWS Signature
// Authorization: Algorithm Credential=access_key_id/date/region/service, SignedHeaders=SignedHeaders, Signature=signature
// Pero adaptado para Xeni
$authorization_header = "Xeni-API Credential={$api_key}/{$date}/xeni/xeni, SignedHeaders=content-type;host, Signature=SIGNATURE_NOT_IMPLEMENTED";

echo "Probando con encabezado de autorización tipo Signature (simulado)...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['key' => $api_key, 'secret' => $secret]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: ' . $authorization_header,
    'Content-Type: application/json',
    'X-Amz-Date: ' . $date,
    'Date: ' . $date_header
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

// Intentar una autenticación más simple con token de API como se hace comúnmente
echo "Probando autenticación con Bearer token (aunque no hay token aún)...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['key' => $api_key, 'secret' => $secret]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $api_key,  // Aunque esto no tendría sentido en login
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

// Probar si podemos obtener el token usando el formato esperado por la documentación
// Quizás la API esperaba que se usara un header personalizado específico
echo "Probando con header personalizado Xeni-Auth...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['key' => $api_key, 'secret' => $secret]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Xeni-Auth: ' . $api_key . ':' . $secret,
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

echo "Pruebas completadas.\n";