# Guía de Troubleshooting para Firestore

## Mejoras Implementadas

Se han implementado las siguientes mejoras para diagnosticar y solucionar problemas de escritura en Firestore:

### 1. Sistema de Logging Completo

- **Archivo de log**: `app/Modules/FirebaseTest/logs/firebase_debug.log`
- **Captura de errores PHP**: Error handler personalizado
- **Shutdown function**: Captura errores fatales que no lanzan excepciones
- **Logging detallado**: Cada operación de Firestore se registra

### 2. Manejo Robusto de Errores

- **Try/catch con Throwable**: Captura TODOS los tipos de errores (Exception, Error, etc.)
- **Verificación de snapshots**: Después de `set()` se verifica que el documento realmente existe
- **Fallback automático**: Si `set()` falla, intenta con `add()`
- **Mensajes informativos**: Se muestra el tipo exacto de error y stack trace completo

### 3. Transporte Configurable (gRPC vs REST)

- **Detección automática**: Verifica si gRPC está disponible
- **Opción manual**: Puedes forzar REST modificando `$useRestTransport = true` en la clase
- **Fallback inteligente**: Si gRPC no está disponible, usa REST automáticamente

## Configuración Actual

**Extensiones instaladas:**
- ✅ gRPC: Instalado
- ✅ Protobuf: Instalado

**Versiones:**
- kreait/firebase-php: 7.22.0
- google/cloud-firestore: v1.53.1

## Cómo Usar

### Opción 1: Interfaz Web

1. Accede a `/firebase-test` en tu navegador
2. Click en "Test Firestore (Database)"
3. Revisa los mensajes en pantalla y el archivo de log

### Opción 2: Script CLI (Recomendado para Debug)

```bash
# Usando gRPC (default)
php app/Modules/FirebaseTest/test_firestore_cli.php

# Forzando REST
php app/Modules/FirebaseTest/test_firestore_cli.php --rest
```

**Ventajas del script CLI:**
- Salida directa en consola
- Más fácil de debuggear
- No depende del servidor web
- Archivo de log separado: `logs/firestore_cli_test.log`

## Checklist de Verificación

Si sigues teniendo problemas, verifica lo siguiente:

### 1. Credenciales

```bash
# En Windows (PowerShell)
$env:FIREBASE_PROJECT_ID
$env:FIREBASE_CLIENT_EMAIL
$env:FIREBASE_PRIVATE_KEY

# En Unix/Linux
echo $FIREBASE_PROJECT_ID
echo $FIREBASE_CLIENT_EMAIL
echo $FIREBASE_PRIVATE_KEY
```

**Importante**: `FIREBASE_PRIVATE_KEY` debe incluir `\n` literales (no saltos de línea reales). El código los convierte automáticamente.

### 2. API de Firestore Habilitada

1. Ve a [Google Cloud Console](https://console.cloud.google.com)
2. Selecciona tu proyecto
3. Navega a "APIs & Services" > "Enabled APIs & services"
4. Verifica que "Cloud Firestore API" esté habilitada

### 3. Permisos IAM

La cuenta de servicio debe tener uno de estos roles:
- `roles/datastore.owner` (para tests)
- `roles/datastore.user` (para producción)
- `Cloud Datastore User`

Verificar en: [IAM & Admin](https://console.cloud.google.com/iam-admin/iam)

### 4. Revisar Logs

**Archivo principal de logs:**
```
app/Modules/FirebaseTest/logs/firebase_debug.log
```

**Busca estas líneas clave:**
- `[INIT] Firebase inicializado correctamente` ✅
- `[INIT] Cliente Firestore creado OK` ✅
- `[FIRESTORE] set() completado sin excepción` ✅
- `[FIRESTORE] Snapshot obtenido, exists: YES` ✅

**Si ves errores, busca:**
- Tipo de error (`get_class()` en el log)
- Mensaje de error
- Stack trace completo

## Forzar Transporte REST

Si sospechas que el problema es con gRPC, fuerza REST:

**En `Main.php` línea 17:**
```php
protected $useRestTransport = true; // Cambiar a true
```

**En CLI:**
```bash
php app/Modules/FirebaseTest/test_firestore_cli.php --rest
```

## Problemas Comunes

### Error: "Permission denied"

**Solución:**
- Verifica roles IAM de la service account
- Asegúrate de que la API está habilitada
- Verifica que el `project_id` sea correcto

### Error: "Failed to load credentials"

**Solución:**
- Verifica que todas las variables de entorno estén configuradas
- Comprueba que `FIREBASE_PRIVATE_KEY` tenga el formato correcto
- Revisa el log para ver el archivo temporal creado

### set() no lanza error pero no escribe

**Diagnóstico:**
- Revisa el log, debería decir: `Snapshot obtenido, exists: NO`
- Esto indica un problema de permisos o configuración del proyecto
- Prueba con el fallback `add()`

### gRPC vs REST

**Cuándo usar REST:**
- En hosting compartido sin extensión gRPC
- Si gRPC da problemas (timeouts, crashes)
- Para debugging más fácil (HTTP requests visibles)

**Cuándo usar gRPC:**
- Producción (mejor performance)
- Si la extensión está disponible y funciona bien

## Debugging Avanzado

### Ver requests HTTP (solo con REST)

Añade Monolog para ver las peticiones HTTP:

```bash
composer require monolog/monolog
```

Luego en `initializeFirebase()` después de línea 100:

```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('firebase');
$logger->pushHandler(new StreamHandler(__DIR__ . '/logs/http_requests.log', Logger::DEBUG));

// Nota: Esta funcionalidad depende de la versión de kreait/firebase-php
```

### Verificar Firestore desde REST API directamente

Prueba esto desde PowerShell/Bash (requiere `gcloud` CLI instalado):

```bash
# Obtener token
gcloud auth application-default print-access-token

# Crear documento (reemplaza PROJECT_ID y ACCESS_TOKEN)
curl -X POST \
  "https://firestore.googleapis.com/v1/projects/PROJECT_ID/databases/(default)/documents/test_collection" \
  -H "Authorization: Bearer ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "fields": {
      "nombre": {"stringValue": "Test directo"},
      "valor": {"integerValue": "42"}
    }
  }'
```

Si esto funciona pero PHP no, el problema es la configuración del cliente PHP.

## Soporte

Si después de seguir todos estos pasos sigues teniendo problemas:

1. **Copia el contenido de `firebase_debug.log`**
2. **Ejecuta el script CLI y copia la salida completa**
3. **Verifica el error exacto** (clase de excepción, mensaje, línea)
4. **Comprueba versiones**: `composer show kreait/firebase-php google/cloud-firestore`

## Archivos Modificados

Los siguientes archivos fueron modificados/creados para implementar estas mejoras:

- `app/Modules/FirebaseTest/Main.php` - Clase principal con mejoras
- `app/Modules/FirebaseTest/test_firestore_cli.php` - Script de prueba CLI
- `app/Modules/FirebaseTest/logs/` - Directorio de logs (creado automáticamente)
- `app/Modules/FirebaseTest/FIRESTORE_TROUBLESHOOTING.md` - Esta guía

## Referencias

- [Documentación kreait/firebase-php](https://firebase-php.readthedocs.io)
- [Google Cloud Firestore REST API](https://cloud.google.com/firestore/docs/reference/rest)
- [gRPC PHP Extension](https://cloud.google.com/php/grpc)
