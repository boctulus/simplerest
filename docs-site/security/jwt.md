---
title: Autenticación JWT
source:
  - docs/security/jwt.md
source_revision: 5192298b785bdcdd8751d38eb6b7be97abb83d4d
status: partial
---

# Autenticación JWT

El core define el contrato `IAuth` y detecta credenciales bearer en `Request`. El controlador JWT que usa esta aplicación proviene del paquete opcional `simplerest-jwt-auth`; `AuthHandler` resuelve las acciones API a `MyAuthController`. La [guía de autenticación](/security/authentication) cubre también API keys y solicitudes guest.

## Configuración de tokens

`config/config.php` usa variables de entorno para los secretos y define ajustes separados:

| Token | Variable de secreto | Algoritmo fuente | Duración del checkout |
| --- | --- | --- | --- |
| Access | `TOKENS_ACCSS_SECRET_KEY` | `HS256` | 3.600 segundos |
| Refresh | `TOKENS_REFSH_SECRET_KEY` | `HS256` | 1.209.600 segundos |
| Email | `TOKENS_EMAIL_SECRET_KEY` | `HS256` | 3.600 segundos |

Son valores de este checkout, no defaults universales. Mantén secretos distintos y fuera del código. El paquete incluye `iat`, `exp`, IP y user-agent; el rechazo por IP o user-agent depende de los switches configurados.

## Login y renovación

`POST /api/v1/auth/login` comprueba los campos definidos por el modelo de usuario y verifica el hash de contraseña. Para una cuenta activa válida, devuelve un access JWT y un refresh JWT. El access payload contiene UID, roles, permisos de tabla/especiales, estado de la cuenta y el acceso a bases de datos que aporte la aplicación. El refresh lleva UID y metadatos comunes, sin la lista de permisos.

La respuesta de login tiene esta forma derivada del código:

```json
{
  "access_token": "<token firmado>",
  "token_type": "bearer",
  "expires_in": 3600,
  "refresh_token": "<token firmado>",
  "roles": [],
  "uid": 0,
  "db_access": []
}
```

`POST /api/v1/auth/token` recibe el refresh JWT en `Authorization: Bearer ...`. Lo valida, recarga el estado de usuario/permisos cuando corresponde y devuelve un access token nuevo. El código inspeccionado no devuelve un refresh token nuevo en esa respuesta. La validación de access token no renueva silenciosamente un token expirado.

## Detección y acciones relacionadas

`Request::getAuth()` prefiere un query param `token` no vacío y lo convierte a bearer; si no, lee las variantes del header Authorization. Aunque la forma por query está implementada, las URLs pueden conservarse en historial, logs y referrers. Para clientes usa el header Authorization. Cuando llegan API key y JWT a la vez, `authMethod()` selecciona primero API key.

El controlador del paquete también define registro, email tokens, cambio de contraseña e impersonación. El flujo `pwdv` de esta aplicación es un override de `MyAuthController`, no una garantía del core. Las acciones de impersonación tienen su propia ruta de refresh token y requieren la capacidad ACL correspondiente.

## Alcance de verificación

El paquete depende de `firebase/php-jwt`; la presencia del manifest no demuestra una instalación limpia en un consumidor. Se inspeccionaron las fuentes de tests sin ejecutarlas. No se hicieron login, renovación, email token ni impersonación.
