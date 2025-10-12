# Firestore con HTTP Puro - Sin Dependencias

## 🎯 Objetivo

Esta implementación usa **SOLO peticiones HTTP directas** a la API REST de Firestore, sin depender de:
- ❌ kreait/firebase-php
- ❌ google/cloud-firestore
- ❌ gRPC
- ❌ Protobuf

**Usa únicamente:**
- ✅ cURL (nativo de PHP)
- ✅ OpenSSL (nativo de PHP)
- ✅ JSON nativo de PHP

## 📁 Archivos Creados

### 1. `FirestoreRawHTTP.php`
Clase principal que implementa:
- Autenticación OAuth2 con JWT firmado manualmente
- Operaciones CRUD en Firestore vía REST API
- Conversión de datos entre formato PHP y formato Firestore
- Logging detallado de todas las operaciones

**Métodos principales:**
```php
$client = new FirestoreRawHTTP($projectId, $clientEmail, $privateKey);

// Autenticación
$token = $client->getAccessToken();

// Crear documento
$result = $client->createDocument('collection_name', $data);

// Leer documento
$result = $client->getDocument('collection_name', 'doc_id');

// Actualizar documento
$result = $client->createDocument('collection_name', $data, 'doc_id');

// Listar documentos
$result = $client->listDocuments('collection_name', 10);
```

### 2. `test_raw_http.php`
Script CLI completo que ejecuta 5 tests:
1. Obtener access token OAuth2
2. Crear documento (POST)
3. Leer documento (GET)
4. Actualizar documento (PATCH)
5. Listar documentos (GET)

**Uso:**
```bash
php app/Modules/FirebaseTest/test_raw_http.php
```

### 3. Integración Web
Añadida función `test_raw_http()` en `Main.php` para acceder desde navegador:
- URL: `http://tu-dominio/firebase-test/raw-http`
- Muestra resultados visuales de todas las operaciones
- Logs detallados disponibles

## 🚀 Cómo Usar

### Opción 1: Script CLI (Recomendado para diagnóstico)

```bash
cd D:\laragon\www\simplerest
php app/Modules/FirebaseTest/test_raw_http.php
```

**Ventajas:**
- Salida en tiempo real
- Fácil de debuggear
- No depende del servidor web
- Log separado en `logs/firestore_raw_http.log`

### Opción 2: Interfaz Web

1. Navega a: `http://localhost/firebase-test`
2. Click en: **"✨ Test HTTP Puro (SIN dependencias, SIN gRPC)"**
3. Verás los resultados de todos los tests en el navegador

### Opción 3: Usar la clase directamente

```php
require_once 'app/Modules/FirebaseTest/FirestoreRawHTTP.php';

use Boctulus\Simplerest\modules\FirebaseTest\FirestoreRawHTTP;

$client = new FirestoreRawHTTP(
    env('FIREBASE_PROJECT_ID'),
    env('FIREBASE_CLIENT_EMAIL'),
    env('FIREBASE_PRIVATE_KEY')
);

// Crear documento
$data = [
    'nombre' => 'Mi Documento',
    'valor' => 123,
    'activo' => true
];

$result = $client->createDocument('mi_coleccion', $data);

if ($result['success']) {
    echo "Documento creado: {$result['documentId']}\n";
} else {
    echo "Error: {$result['error']}\n";
}
```

## 🔍 Cómo Funciona

### 1. Autenticación OAuth2

La autenticación se hace manualmente en 3 pasos:

**Paso 1: Crear JWT**
```php
// Header
$header = ['alg' => 'RS256', 'typ' => 'JWT'];

// Payload
$payload = [
    'iss' => $clientEmail,
    'sub' => $clientEmail,
    'aud' => 'https://oauth2.googleapis.com/token',
    'iat' => time(),
    'exp' => time() + 3600,
    'scope' => 'https://www.googleapis.com/auth/datastore'
];

// Firmar con private key usando OpenSSL
$signature = openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
$jwt = "$header.$payload.$signature";
```

**Paso 2: Intercambiar JWT por Access Token**
```php
POST https://oauth2.googleapis.com/token
Content-Type: application/x-www-form-urlencoded

grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer
assertion=<JWT>
```

**Paso 3: Usar Access Token**
```php
GET https://firestore.googleapis.com/v1/projects/{projectId}/databases/(default)/documents/...
Authorization: Bearer <access_token>
```

### 2. Formato de Datos Firestore

Firestore usa un formato específico para los datos:

**PHP → Firestore:**
```php
// PHP
['nombre' => 'Juan', 'edad' => 30, 'activo' => true]

// Se convierte a:
{
  "fields": {
    "nombre": {"stringValue": "Juan"},
    "edad": {"integerValue": "30"},
    "activo": {"booleanValue": true}
  }
}
```

**La clase hace esta conversión automáticamente.**

### 3. Operaciones REST

| Operación | Método HTTP | URL |
|-----------|-------------|-----|
| Crear | POST | `/projects/{projectId}/databases/(default)/documents/{collection}` |
| Leer | GET | `/projects/{projectId}/databases/(default)/documents/{collection}/{docId}` |
| Actualizar | PATCH | `/projects/{projectId}/databases/(default)/documents/{collection}/{docId}` |
| Listar | GET | `/projects/{projectId}/databases/(default)/documents/{collection}` |
| Eliminar | DELETE | `/projects/{projectId}/databases/(default)/documents/{collection}/{docId}` |

## 📊 Logs

Todos los logs se guardan en:
- **CLI**: `app/Modules/FirebaseTest/logs/firestore_raw_http.log`
- **Web**: Mismo archivo

Cada operación registra:
- Timestamp
- Acción realizada
- URLs llamadas
- Códigos HTTP de respuesta
- Errores (si ocurren)

## ✅ Ventajas de Este Enfoque

1. **Sin dependencias complejas**: No necesitas instalar gRPC ni Protobuf
2. **Debugging fácil**: Ves exactamente qué se envía a Firebase
3. **Control total**: Sabes exactamente qué está pasando
4. **Portable**: Funciona en cualquier servidor con PHP + cURL + OpenSSL
5. **Aísla problemas**: Si esto funciona, el problema está en las librerías

## ❗ Troubleshooting

### Error: "Error cargando private key"

**Problema:** La private key no tiene el formato correcto.

**Solución:**
```bash
# Verifica las credenciales
php app/Modules/FirebaseTest/verify_credentials.php
```

La private key debe:
- Empezar con `-----BEGIN PRIVATE KEY-----`
- Terminar con `-----END PRIVATE KEY-----`
- Tener `\n` literales en el .env (no saltos de línea reales)

### Error: "HTTP 401" o "HTTP 403"

**Problema:** Credenciales incorrectas o sin permisos.

**Verifica:**
1. `FIREBASE_PROJECT_ID` es correcto
2. `FIREBASE_CLIENT_EMAIL` es el email de la service account
3. La service account tiene rol `Cloud Datastore User` o superior
4. La API de Firestore está habilitada en Google Cloud Console

### Error: "HTTP 404"

**Problema:** Proyecto o colección no existe.

**Verifica:**
1. El `project_id` es correcto
2. Firestore está configurado en modo "Native" (no Datastore)

### Error: "cURL error"

**Problema:** Problema de red o certificados.

**Solución:**
```bash
# Verifica que cURL funciona
php -r "echo file_get_contents('https://www.google.com');"
```

## 🔐 Seguridad

**Importante:**
- Las credenciales están en `.env` y NO deben committearse a git
- El `.env` debe estar en `.gitignore`
- En producción, usa variables de entorno del servidor
- Los logs pueden contener información sensible (tokens)

## 📚 Referencias

- [Firestore REST API](https://firebase.google.com/docs/firestore/reference/rest)
- [Google OAuth2 JWT](https://developers.google.com/identity/protocols/oauth2/service-account)
- [Firestore Data Types](https://firebase.google.com/docs/firestore/reference/rest/v1/Value)

## 🆘 Necesitas Ayuda?

Si este método **HTTP puro funciona**, entonces el problema está en las librerías PHP (kreait/firebase-php o google/cloud-firestore).

Si este método **también falla**, entonces el problema es:
- Credenciales incorrectas
- Permisos IAM
- API no habilitada
- Problema de red/firewall

Revisa los logs en `logs/firestore_raw_http.log` para detalles exactos.
