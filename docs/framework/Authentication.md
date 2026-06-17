# Autenticación JWT — SimpleRest

## Arquitectura

El sistema de autenticación usa **JWT (JSON Web Tokens)** con un esquema de **dual token** (access + refresh).

**Clase principal**: `src/framework/Api/AuthController.php` (1435 líneas)

---

## Flujo de Autenticación

```
Request → /auth/login
  → Validar credenciales (email + password)
  → Generar Access Token (corto plazo)
  → Generar Refresh Token (largo plazo)
  → Responder con ambos tokens

Request con token → /api/v1/...
  → Validar Access Token (JWT decode + verify)
  → Identificar usuario
  → Ejecutar acción
  → Refrescar token si expiró
```

## Endpoints

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/auth/register` | Registro de usuario |
| POST | `/auth/login` | Inicio de sesión |
| POST | `/auth/logout` | Cierre de sesión |
| POST | `/auth/refresh` | Refrescar token |
| POST | `/auth/password-reset` | Solicitar reset password |
| POST | `/auth/password-reset/confirm` | Confirmar reset |
| POST | `/auth/verify-email` | Verificar email |
| GET | `/auth/me` | Datos del usuario actual |
| GET | `/api/v1/auth/me` | Datos del usuario actual (API) |

También disponibles bajo `/api/v1/auth/*`.

## Configuración

```php
// config/config.php
'access_token' => [
    'secret_key'     => env('JWT_SECRET'),
    'expiration_time' => 3600,        // 1 hora
    'encryption'      => 'HS256',
],
'refresh_token' => [
    'secret_key'     => env('JWT_REFRESH_SECRET'),
    'expiration_time' => 1209600,      // 14 días
    'encryption'      => 'HS256',
],
'remember_me_token' => [
    'secret_key'     => env('JWT_REMEMBER_SECRET'),
    'expiration_time' => 2592000,      // 30 días
    'encryption'      => 'HS256',
],
```

## Estructura del Token

```json
{
  "alg": "HS256",
  "typ": "JWT",
  "iat": 1700000000,
  "exp": 1700003600,
  "ip": "192.168.1.1",
  "user_agent": "Mozilla/5.0 ...",
  "sub": 123,
  "email": "user@example.com",
  "role": "admin"
}
```

## Uso en Controladores

```php
// Obtener usuario autenticado
$userId = auth()::getCurrentUserId();
$user   = auth()::getCurrentUser();

// Verificar si está autenticado
if (auth()::isAuthenticated()) {
    // ...
}

// Obtener token actual
$token = Request::getInstance()->bearerToken();
```

## Social Auth (OAuth)

Configurado para:
- Google OAuth
- Facebook OAuth

## Tabla de Usuarios

Configurable vía `config.php`:

```php
'users_table'    => 'users',
'users_password_field' => 'password',
'users_email_field'    => 'email',
'users_username_field' => 'username',
```

## Ver También

- [`ACL.md`](./ACL.md) — permisos y roles post-autenticación
- [`config/config.php`](../config/config.php) — configuración completa
