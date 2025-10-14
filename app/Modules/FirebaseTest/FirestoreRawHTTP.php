<?php
/**
 * Cliente HTTP puro para Firestore (sin dependencias, sin gRPC)
 * Solo usa cURL y funciones nativas de PHP
 */

namespace Boctulus\Simplerest\Modules\FirebaseTest;

class FirestoreRawHTTP
{
    private $projectId;
    private $clientEmail;
    private $privateKey;
    private $accessToken;
    private $tokenExpiry;
    private $logFile;

    public function __construct($projectId, $clientEmail, $privateKey, $logFile = null)
    {
        $this->projectId = $projectId;
        $this->clientEmail = $clientEmail;
        $this->privateKey = $privateKey;
        $this->logFile = $logFile ?: __DIR__ . '/logs/firestore_raw_http.log';

        // Crear directorio de logs
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $this->log("FirestoreRawHTTP iniciado para proyecto: $projectId");
    }

    private function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        error_log($logMessage, 3, $this->logFile);
        echo $logMessage;
    }

    /**
     * Crear JWT (JSON Web Token) manualmente
     */
    private function createJWT()
    {
        $this->log("Creando JWT...");

        // Header
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];

        // Payload
        $now = time();
        $expiry = $now + 3600; // 1 hora

        $payload = [
            'iss' => $this->clientEmail,
            'sub' => $this->clientEmail,
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $expiry,
            'scope' => 'https://www.googleapis.com/auth/datastore'
        ];

        // Codificar
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
        $dataToSign = "$headerEncoded.$payloadEncoded";

        $this->log("JWT data to sign: " . substr($dataToSign, 0, 100) . "...");

        // Firmar con private key
        $privateKey = $this->privateKey;

        // Asegurarse de que tenga saltos de línea correctos
        if (strpos($privateKey, '\\n') !== false) {
            $privateKey = str_replace('\\n', "\n", $privateKey);
        }

        // Verificar formato de la clave
        if (strpos($privateKey, '-----BEGIN PRIVATE KEY-----') === false) {
            throw new \Exception("Private key no tiene el formato correcto. Debe comenzar con -----BEGIN PRIVATE KEY-----");
        }

        $this->log("Private key length: " . strlen($privateKey));

        // Crear recurso de clave
        $keyResource = openssl_pkey_get_private($privateKey);

        if ($keyResource === false) {
            $error = openssl_error_string();
            throw new \Exception("Error cargando private key: $error");
        }

        $this->log("Private key cargada correctamente");

        // Firmar
        $signature = '';
        $success = openssl_sign($dataToSign, $signature, $keyResource, OPENSSL_ALGO_SHA256);

        if (!$success) {
            $error = openssl_error_string();
            throw new \Exception("Error firmando JWT: $error");
        }

        $this->log("JWT firmado correctamente");

        $signatureEncoded = $this->base64UrlEncode($signature);
        $jwt = "$dataToSign.$signatureEncoded";

        $this->log("JWT creado: " . substr($jwt, 0, 50) . "...");

        return $jwt;
    }

    /**
     * Base64 URL encode (sin padding)
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Obtener access token de Google OAuth2
     */
    public function getAccessToken($forceRefresh = false)
    {
        // Si ya tenemos un token válido, usarlo
        if (!$forceRefresh && $this->accessToken && $this->tokenExpiry > time() + 300) {
            $this->log("Usando token existente (expira en " . ($this->tokenExpiry - time()) . " segundos)");
            return $this->accessToken;
        }

        $this->log("Obteniendo nuevo access token...");

        try {
            // Crear JWT
            $jwt = $this->createJWT();

            // Intercambiar JWT por access token
            $tokenUrl = 'https://oauth2.googleapis.com/token';

            $postData = [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ];

            $this->log("Solicitando access token a: $tokenUrl");

            $ch = curl_init($tokenUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                throw new \Exception("cURL error: $curlError");
            }

            $this->log("HTTP Code: $httpCode");
            $this->log("Response: $response");

            if ($httpCode !== 200) {
                throw new \Exception("Error obteniendo access token. HTTP $httpCode: $response");
            }

            $tokenData = json_decode($response, true);

            if (!isset($tokenData['access_token'])) {
                throw new \Exception("Access token no encontrado en respuesta: $response");
            }

            $this->accessToken = $tokenData['access_token'];
            $this->tokenExpiry = time() + ($tokenData['expires_in'] ?? 3600);

            $this->log("✓ Access token obtenido correctamente (expira en {$tokenData['expires_in']} segundos)");
            $this->log("Token: " . substr($this->accessToken, 0, 30) . "...");

            return $this->accessToken;

        } catch (\Exception $e) {
            $this->log("✗ Error obteniendo access token: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Escribir un documento en Firestore
     */
    public function createDocument($collection, $data, $documentId = null)
    {
        $this->log("\n--- CREAR DOCUMENTO ---");
        $this->log("Colección: $collection");
        $this->log("Datos: " . json_encode($data));

        $token = $this->getAccessToken();

        // Construir URL
        if ($documentId) {
            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/$collection/$documentId";
            $method = 'PATCH';
            $url .= '?updateMask.fieldPaths=' . implode('&updateMask.fieldPaths=', array_keys($data));
        } else {
            $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/$collection";
            $method = 'POST';
        }

        $this->log("URL: $url");
        $this->log("Method: $method");

        // Convertir datos al formato de Firestore
        $firestoreData = $this->convertToFirestoreFormat($data);
        $body = json_encode(['fields' => $firestoreData]);

        $this->log("Body: $body");

        // Hacer petición
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($body)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            $this->log("✗ cURL error: $curlError");
            throw new \Exception("cURL error: $curlError");
        }

        $this->log("HTTP Code: $httpCode");
        $this->log("Response: " . substr($response, 0, 500));

        if ($httpCode >= 200 && $httpCode < 300) {
            $responseData = json_decode($response, true);
            $docName = $responseData['name'] ?? 'unknown';
            $parts = explode('/', $docName);
            $docId = end($parts);

            $this->log("✓ Documento creado exitosamente");
            $this->log("Document ID: $docId");

            return [
                'success' => true,
                'documentId' => $docId,
                'fullName' => $docName,
                'data' => $responseData
            ];
        } else {
            $this->log("✗ Error HTTP $httpCode: $response");
            return [
                'success' => false,
                'error' => "HTTP $httpCode",
                'message' => $response
            ];
        }
    }

    /**
     * Leer un documento de Firestore
     */
    public function getDocument($collection, $documentId)
    {
        $this->log("\n--- LEER DOCUMENTO ---");
        $this->log("Colección: $collection");
        $this->log("Document ID: $documentId");

        $token = $this->getAccessToken();

        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/$collection/$documentId";

        $this->log("URL: $url");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            $this->log("✗ cURL error: $curlError");
            throw new \Exception("cURL error: $curlError");
        }

        $this->log("HTTP Code: $httpCode");

        if ($httpCode === 200) {
            $responseData = json_decode($response, true);
            $convertedData = $this->convertFromFirestoreFormat($responseData['fields'] ?? []);

            $this->log("✓ Documento leído exitosamente");
            $this->log("Datos: " . json_encode($convertedData));

            return [
                'success' => true,
                'data' => $convertedData,
                'raw' => $responseData
            ];
        } elseif ($httpCode === 404) {
            $this->log("✗ Documento no encontrado");
            return [
                'success' => false,
                'error' => 'not_found'
            ];
        } else {
            $this->log("✗ Error HTTP $httpCode: $response");
            return [
                'success' => false,
                'error' => "HTTP $httpCode",
                'message' => $response
            ];
        }
    }

    /**
     * Listar documentos de una colección
     */
    public function listDocuments($collection, $limit = 10)
    {
        $this->log("\n--- LISTAR DOCUMENTOS ---");
        $this->log("Colección: $collection");
        $this->log("Límite: $limit");

        $token = $this->getAccessToken();

        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/$collection?pageSize=$limit";

        $this->log("URL: $url");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            $this->log("✗ cURL error: $curlError");
            throw new \Exception("cURL error: $curlError");
        }

        $this->log("HTTP Code: $httpCode");

        if ($httpCode === 200) {
            $responseData = json_decode($response, true);
            $documents = [];

            foreach ($responseData['documents'] ?? [] as $doc) {
                $parts = explode('/', $doc['name']);
                $docId = end($parts);
                $documents[] = [
                    'id' => $docId,
                    'data' => $this->convertFromFirestoreFormat($doc['fields'] ?? [])
                ];
            }

            $this->log("✓ " . count($documents) . " documentos encontrados");

            return [
                'success' => true,
                'documents' => $documents,
                'count' => count($documents)
            ];
        } else {
            $this->log("✗ Error HTTP $httpCode: $response");
            return [
                'success' => false,
                'error' => "HTTP $httpCode",
                'message' => $response
            ];
        }
    }

    /**
     * Convertir datos PHP al formato de Firestore
     */
    private function convertToFirestoreFormat($data)
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $result[$key] = ['booleanValue' => $value];
            } elseif (is_int($value)) {
                $result[$key] = ['integerValue' => (string)$value];
            } elseif (is_float($value)) {
                $result[$key] = ['doubleValue' => $value];
            } elseif (is_string($value)) {
                $result[$key] = ['stringValue' => $value];
            } elseif (is_null($value)) {
                $result[$key] = ['nullValue' => null];
            } elseif (is_array($value)) {
                // Simplificado: tratar como map
                $result[$key] = ['mapValue' => ['fields' => $this->convertToFirestoreFormat($value)]];
            } else {
                $result[$key] = ['stringValue' => (string)$value];
            }
        }

        return $result;
    }

    /**
     * Convertir datos de formato Firestore a PHP
     */
    private function convertFromFirestoreFormat($fields)
    {
        $result = [];

        foreach ($fields as $key => $value) {
            if (isset($value['booleanValue'])) {
                $result[$key] = $value['booleanValue'];
            } elseif (isset($value['integerValue'])) {
                $result[$key] = (int)$value['integerValue'];
            } elseif (isset($value['doubleValue'])) {
                $result[$key] = $value['doubleValue'];
            } elseif (isset($value['stringValue'])) {
                $result[$key] = $value['stringValue'];
            } elseif (isset($value['nullValue'])) {
                $result[$key] = null;
            } elseif (isset($value['mapValue'])) {
                $result[$key] = $this->convertFromFirestoreFormat($value['mapValue']['fields'] ?? []);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function getLogFile()
    {
        return $this->logFile;
    }
}
