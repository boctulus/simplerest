# OpenFactura API - Guía de uso de API Key

## Configuración de API Keys

Las API keys se configuran en el archivo `.env`:

```env
OPENFACTURA_SANDBOX=true
OPENFACTURA_API_KEY_DEV="928e15a2d14d4a6292345f04960f4bd3"
OPENFACTURA_API_KEY_PROD="04f1d39392684b0a9e78ff2a3d0b167a"
```

- **OPENFACTURA_SANDBOX**: Define si se usa el entorno de sandbox (`true`) o producción (`false`)
- **OPENFACTURA_API_KEY_DEV**: API key para el entorno de desarrollo/sandbox
- **OPENFACTURA_API_KEY_PROD**: API key para el entorno de producción

## Formas de enviar la API Key (Override)

El sistema soporta **3 formas** de enviar una API key personalizada, con el siguiente orden de prioridad:

### 1. Headers (Prioridad ALTA) ✅ RECOMENDADO

```bash
curl -X GET "http://example.com/api/v1/openfactura/health" \
  -H "Content-Type: application/json" \
  -H "X-Openfactura-Api-Key: YOUR_API_KEY" \
  -H "X-Openfactura-Sandbox: true"
```

**Ventajas:**
- Más seguro (no aparece en logs del servidor web)
- Estándar en APIs RESTful
- No contamina la URL

### 2. Query Parameters (Prioridad MEDIA)

```bash
curl -X GET "http://example.com/api/v1/openfactura/health?api_key=YOUR_API_KEY&sandbox=true" \
  -H "Content-Type: application/json"
```

También soporta camelCase:
```bash
curl -X GET "http://example.com/api/v1/openfactura/health?apiKey=YOUR_API_KEY&sandbox=true" \
  -H "Content-Type: application/json"
```

**Ventajas:**
- Fácil de usar para testing rápido
- Compatible con navegadores

**Desventajas:**
- Menos seguro (queda en logs del servidor)
- No recomendado para producción

### 3. Body (Prioridad BAJA)

```bash
curl -X POST "http://example.com/api/v1/openfactura/dte/emit" \
  -H "Content-Type: application/json" \
  -d '{
    "api_key": "YOUR_API_KEY",
    "sandbox": true,
    "dteData": {...}
  }'
```

También soporta camelCase:
```bash
curl -X POST "http://example.com/api/v1/openfactura/dte/emit" \
  -H "Content-Type: application/json" \
  -d '{
    "apiKey": "YOUR_API_KEY",
    "sandbox": true,
    "dteData": {...}
  }'
```

**Ventajas:**
- Útil cuando ya estás enviando un body JSON

**Desventajas:**
- Solo funciona con métodos POST/PUT/PATCH
- Mezcla credenciales con datos de negocio

## Orden de Prioridad

Si se envía la API key por múltiples canales, el sistema usa este orden:

1. **Headers** (se usa si está presente)
2. **Query Parameters** (se usa si NO está en headers)
3. **Body** (se usa si NO está en headers NI query params)
4. **ENV** (se usa si no se proporcionó override)

## Ejemplos Prácticos

### Ejemplo 1: Usar API key configurada en .env

```bash
# No es necesario enviar api_key, usará la del .env
curl -X GET "http://simplerest.lan/api/v1/openfactura/health" \
  -H "Content-Type: application/json"
```

### Ejemplo 2: Override con headers (recomendado)

```bash
curl -X GET "http://simplerest.lan/api/v1/openfactura/health" \
  -H "Content-Type: application/json" \
  -H "X-Openfactura-Api-Key: 928e15a2d14d4a6292345f04960f4bd3" \
  -H "X-Openfactura-Sandbox: true"
```

### Ejemplo 3: Override con query params

```bash
curl -X GET "http://simplerest.lan/api/v1/openfactura/sales-registry/2024/11?api_key=928e15a2d14d4a6292345f04960f4bd3&sandbox=true" \
  -H "Content-Type: application/json"
```

### Ejemplo 4: Override con body

```bash
curl -X POST "http://simplerest.lan/api/v1/openfactura/dte/emit" \
  -H "Content-Type: application/json" \
  -d '{
    "api_key": "928e15a2d14d4a6292345f04960f4bd3",
    "sandbox": true,
    "dteData": {
      "Encabezado": {...},
      "Detalle": [...]
    }
  }'
```

## Verificar qué API key se está usando

Todos los endpoints retornan un campo `using_override` que indica si se usó una API key de override:

```json
{
  "success": true,
  "data": {
    "service": "OpenFactura API",
    "status": "healthy",
    "sandbox": true,
    "using_override": true  // ← true si se usó override, false si se usó .env
  }
}
```

## Notas de Seguridad

1. **Nunca** commitees tu API key real en el repositorio
2. Usa **headers** para producción, no query params
3. El modo sandbox (`sandbox: true`) es solo para desarrollo
4. En producción, las API keys deben estar **solo en .env**, no en el código

## Troubleshooting

### Mensaje "API key de OpenFactura no configurada"

Significa que:
- No hay API key en `.env`
- NO se envió override por headers/query/body

**Solución:** Verifica que `.env` tenga:
```env
OPENFACTURA_API_KEY_DEV="tu_api_key_aqui"
```

### Mensaje "Api no encontrada"

Este mensaje viene directamente del API de OpenFactura. Posibles causas:
- El endpoint no existe en el API de OpenFactura
- Los datos enviados (RUT, folio, tipo) no existen
- El endpoint requiere permisos especiales

**Esto NO es un error del controlador**, es una respuesta válida del API remoto.

---

**Autor:** Pablo Bozzolo (boctulus)
**Fecha:** 2025-11-28
