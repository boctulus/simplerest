# Documentación de Autenticación - API Xeni

## Información General

- **Endpoint base**: `https://uat.travelapi.ai`
- **API Key**: `96989ee3-5c9c-4557-851c-40d292ab4319`
- **Secret**: `M$72tYWz$3ZJJ71`
- **Documentación oficial**: `https://developer.xeni.com/`
- **Entorno**: Sandbox para pruebas

## Métodos de Autenticación Probados

### 1. Credenciales en el cuerpo de la solicitud (método original)
```json
{
  "key": "96989ee3-5c9c-4557-851c-40d292ab4319",
  "secret": "M$72tYWz$3ZJJ71"
}
```
**Headers**:
```
Content-Type: application/json
```

**Resultado**: HTTP 403 - `Missing Authentication Token`

### 2. Credenciales como encabezados personalizados
**Headers**:
```
X-API-Key: 96989ee3-5c9c-4557-851c-40d292ab4319
X-API-Secret: M$72tYWz$3ZJJ71
Content-Type: application/json
```

**Resultado**: HTTP 403 - `Missing Authentication Token`

### 3. Autenticación básica HTTP
**Headers**:
```
Authorization: Basic [base64(key:secret)]
Content-Type: application/json
```

**Resultado**: HTTP 403 - `Authorization header requires 'Credential' parameter. Authorization header requires 'Signature' parameter. Authorization header requires 'SignedHeaders' parameter. Authorization header requires existence of either a 'X-Amz-Date' or a 'Date' header. (Hashed with SHA-256 and encoded with Base64)`

### 4. API Key en encabezado Authorization
**Headers**:
```
Authorization: ApiKey 96989ee3-5c9c-4557-851c-40d292ab4319
Content-Type: application/json
```

**Resultado**: HTTP 403 - `Invalid key=value pair (missing equal-sign) in Authorization header (hashed with SHA-256 and encoded with Base64)`

### 5. Parámetros de consulta
```
/auth/login?key=96989ee3-5c9c-4557-851c-40d292ab4319&secret=M$72tYWz$3ZJJ71
```

**Headers**:
```
Content-Type: application/x-www-form-urlencoded
```

**Resultado**: HTTP 403 - `Missing Authentication Token`

### 6. Autenticación con encabezados Date/X-Amz-Date
**Headers**:
```
Authorization: Basic [base64(key:secret)]
Date: [RFC 2822 format]
Content-Type: application/json
```

**Headers alternativo**:
```
Authorization: Basic [base64(key:secret)]
X-Amz-Date: [ISO 8601 format]
Content-Type: application/json
```

**Resultado**: HTTP 403 - `Authorization header requires 'Credential' parameter. Authorization header requires 'Signature' parameter. Authorization header requires 'SignedHeaders' parameter.`

### 7. Autenticación tipo AWS Signature (simulada)
**Headers**:
```
Authorization: Xeni-API Credential=key/date/xeni/xeni, SignedHeaders=content-type;host, Signature=SIGNATURE_NOT_IMPLEMENTED
X-Amz-Date: [timestamp]
Date: [timestamp]
Content-Type: application/json
```

**Resultado**: HTTP 403 - `Missing Authentication Token`

### 8. Prueba con header personalizado Xeni-Auth
**Headers**:
```
Xeni-Auth: key:secret
Content-Type: application/json
```

**Resultado**: HTTP 403 - `Missing Authentication Token`

## Análisis de Resultados

### Resultados Clave

1. **El endpoint `/auth/login` existe** - Todas las pruebas recibieron respuesta del servidor (HTTP 403, no 404)

2. **Sistema de autenticación global** - El servidor devuelve "Missing Authentication Token" incluso para solicitudes básicas sin autenticación

3. **Posible esquema tipo AWS Signature** - El mensaje de error menciona 'Credential', 'Signature', 'SignedHeaders' y 'X-Amz-Date', lo cual es característico de AWS Signature v4

4. **Formato específico de encabezado** - El sistema espera un encabezado de autorización con estructura específica que no coincide con los formatos estándar

## Conclusiones

### 1. El problema NO es de conectividad
- El servidor responde consistentemente (HTTP 403)
- El endpoint existe y está activo

### 2. El problema es de formato de autenticación
- Los mensajes de error indican que se espera un formato específico
- La API implementa un esquema de autenticación personalizado o poco común

### 3. Posibles razones para el fallo
- Las credenciales proporcionadas pueden no estar activas en el entorno de pruebas actual
- El esquema de autenticación ha cambiado y no está reflejado en la documentación.
- Se requiere un proceso de autenticación multipartita (múltiples pasos).
- Se requiere un encabezado o parámetro adicional no mencionado en la documentación superficial.

### 4. Recomendaciones
1. **Contactar soporte de Xeni** para aclarar el formato exacto de autenticación
2. **Verificar la documentación completa** en `https://developer.xeni.com/` para detalles específicos de autenticación
3. **Validar credenciales** en el entorno de sandbox
4. **Considerar la posibilidad de que se requiera un proceso de firma HMAC** o similar

## Estado Actual

- **Framework**: ✅ Correctamente configurado para interactuar con la API
- **Rutas**: ✅ Funcionando correctamente  
- **Controladores**: ✅ Implementados y listos
- **Autenticación**: ❌ Pendiente de determinar formato correcto

## Referencias

- **Documentación oficial**: https://developer.xeni.com/
- **Endpoint de autenticación**: `POST /auth/login`
- **Credenciales para sandbox**:
  - API Key: `96989ee3-5c9c-4557-851c-40d292ab4319`
  - Secret: `M$72tYWz$3ZJJ71`
  - Base URL: `https://uat.travelapi.ai/`

## Notas Adicionales

Dado que se han probado múltiples formatos estándar de autenticación sin éxito, es probable que:

1. La API Xeni utilice un esquema de autenticación personalizado
2. Las credenciales proporcionadas requieran activación previa
3. Exista un proceso de autenticación de múltiples pasos
4. Se requiera un encabezado personalizado específico no documentado públicamente

Se recomienda contactar directamente al equipo de soporte de Xeni para obtener detalles específicos del formato de autenticación requerido.