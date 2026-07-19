# Autenticación JWT — SimpleRest

## Arquitectura

El sistema de autenticación usa **JWT (JSON Web Tokens)** con un esquema de **dual token** (access + refresh).

**Clase principal**: `src/framework/Api/AuthController.php` (1431 líneas)

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
| POST | `/auth/rememberme` | Solicitar reset password (email) |
| GET | `/auth/change_pass_by_link/{jwt}/{exp}` | Validar enlace de reset |
| POST | `/auth/change_pass_process` | Aplicar nueva contraseña |
| POST | `/auth/verify-email` | Verificar email |
| GET | `/auth/me` | Datos del usuario actual |
| GET | `/api/v1/auth/me` | Datos del usuario actual (API) |

También disponibles bajo `/api/v1/auth/*`.

## Configuración

```php
// config/config.php
'access_token' => [
    'secret_key'     => env('TOKENS_ACCSS_SECRET_KEY'),
    'expiration_time' => 60 * 15 * 50000,   // access token TTL
    'encryption'      => 'HS256',
],
'refresh_token' => [
    'secret_key'     => env('TOKENS_REFSH_SECRET_KEY'),
    'expiration_time' => 315360000,          // ~10 años
    'encryption'      => 'HS256',
],
'email_token' => [
    'secret_key'     => env('TOKENS_EMAIL_SECRET_KEY'),  // >=32 chars, DIFERENTE del access
    'expires_in'     => 3600,                             // 1 hora
    'encryption'      => 'HS256',
],
```

> **Seguridad**: `email_token.secret_key` debe ser >=32 caracteres y **diferente** de `access_token.secret_key`. El framework valida esto en runtime y lanza error 500 si no se cumple.

## Estructura del Token

### Access Token

```json
{
  "alg": "HS256",
  "typ": "JWT",
  "iat": 1700000000,
  "exp": 1700003600,
  "ip": "192.168.1.1",
  "user_agent": "Mozilla/5.0 ...",
  "uid": 123,
  "roles": ["admin"],
  "permissions": { "tb": {}, "sp": [] },
  "is_active": 1
}
```

### Password Reset Token

```json
{
  "alg": "HS256",
  "typ": "JWT",
  "iat": 1700000000,
  "nbf": 1700000000,
  "exp": 1700003600,
  "uid": 123,
  "purpose": "password_reset",
  "pwdv": "a1b2c3d4..."
}
```

**`pwdv`** = `HMAC-SHA256(currentPasswordHash, emailSecret)`. Al cambiar la contraseña, el hash cambia y `pwdv` ya no coincide → token inválido (single-use).

## Password Reset (Seguro)

### Flujo completo

```
1. POST /auth/rememberme
   Body: { "email": "user@example.com" }
   → Responde siempre: { "message": "If the account exists, a password reset link will be sent." }
   → Si el email existe:
     a. Busca usuario activo (UsersModel::findPasswordResetCandidate)
     b. Genera JWT con pwdv = HMAC(currentHash, emailSecret)
     c. Genera URL: /reset-password?token=...&exp=...
     d. Envía email con enlace
   → Si el email no existe: no hace nada (respuesta idéntica)

2. GET /auth/change_pass_by_link/{jwt}/{exp}
   → Valida JWT (firma, expiración, purpose=pwdv)
   → Verifica pwdv contra hash actual de la DB
   → Responde: { "valid": true, "expires_at": ... }
   → Si el token fue rechazado: error 400 genérico

3. POST /auth/change_pass_process
   Headers: Authorization: Bearer {reset_token}
   Body: { "password": "newpass123", "password_confirmation": "newpass123" }
   → Valida token + pwdv
   → Actualiza contraseña (UsersModel::updatePasswordFromReset)
   → Responde: { "message": "Password changed successfully." }
   → NO concede access/refresh tokens (debe hacer login)
```

### Seguridad del token de reset

| Aspecto | Detalle |
|---------|---------|
| Expiración | 1 hora (`email_token.expires_in`) |
| Single-use | pwdv invalidado al cambiar password |
| Secret dedicado | >=32 chars, diferente del access token |
| Propósito | `purpose: "password_reset"` (validado en decode) |
| Respuesta neutra | No revela si el email existe |
| Sin tokens al cambiar | Solo confirma éxito, no sesiona |

### Overrides en MyAuthController

`app/Controllers/MyAuthController.php` sobreescribe los métodos del framework:

- `rememberme()` — genera token seguro + envía email
- `change_pass_by_link()` — valida token sin conceder sesiones
- `change_pass_process()` — aplica password sin access tokens

Los hooks del framework (`onRemembered`, `onChangedPassword`, etc.) se conservan.

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
- [`CHANGELOG-password-reset-security.md`](../CHANGELOG-password-reset-security.md) — detalle del overhaul de seguridad
