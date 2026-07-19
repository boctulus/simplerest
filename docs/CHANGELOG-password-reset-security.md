# CHANGELOG — Password Reset Security Overhaul

**Fecha**: 2026-07-19  
**Severidad**: Crítica (seguridad)  
**Alcance**: Backend (framework + app layer)

---

## Resumen

Se reescribió el flujo de password reset para eliminar vulnerabilidades de seguridad:
- Tokens de un solo uso basados en hash de la contraseña actual (`pwdv`)
- Secret JWT dedicado para email tokens (independiente del access token)
- Expiración reducida de 7 días a 1 hora
- Respuestas neutras para prevenir enumeración de cuentas
- Corrección de 3 bugs en el envío de emails (Mail.php)

---

## Cambios

### 1. `src/framework/Libs/Mail.php` — 3 bugs corregidos

| Bug | Línea | Descripción |
|-----|-------|-------------|
| cc vacío | 155 | `Arrays::isAssoc($cc)` se ejecutaba con array vacío → crash. Agregado guard `!empty($cc) &&` |
| bcc vacío | 165 | Mismo bug que cc. Agregado guard `!empty($bcc) &&` |
| retorno en fallo | 292 | Retornaba `static::$errors` (string) pero firma dice `: bool`. Ahora retorna `false` |

### 2. `app/Models/main/UsersModel.php` — Métodos de reset agregados

```php
// Busca usuario activo por email (para generar token)
findPasswordResetCandidate(string $email): ?array

// Busca usuario activo por ID (para validar token)
findActiveForPasswordReset(int $userId): ?array

// Actualiza password desde reset (hash automático)
updatePasswordFromReset(int $userId, string $password): bool
```

**Mejora adicional**: `registerInputMutator()` ahora verifica `password_get_info()` para evitar doble-hash.

### 3. `app/Controllers/MyAuthController.php` — Override seguro

| Método | Acción |
|--------|--------|
| `rememberme()` | Genera JWT con `pwdv` (HMAC del hash), envía email, responde neutro |
| `change_pass_by_link()` | Valida token + expiración + `pwdv` contra hash actual |
| `change_pass_process()` | Aplica nueva contraseña, no concede access tokens |

**Mecanismo de single-use**: El token JWT contiene `pwdv = HMAC(currentHash, secret)`. Al cambiar la contraseña, el hash cambia, y `pwdv` ya no coincide → token inválido.

**Hooks del framework conservados**: `onRegister`, `onRemembered`, `onLogged`, `onChangedPassword`, etc.

### 4. `config/config.php` — Expiración reducida

```php
// Antes:
'email_token' => ['expires_in' => 7 * 24 * 3600]  // 7 días

// Ahora:
'email_token' => ['expires_in' => 3600]  // 1 hora
```

### 5. `unit-tests/auth/PasswordResetTest.php` — Nuevo

4 tests / 35 assertions:
- `testPasswordUpdateHashesPlainTextAndDoesNotDoubleHash`
- `testResetTokenIsValidUntilPasswordChangesAndThenCannotBeReplayed`
- `testResetTokenRejectsManipulatedExpiration`
- `testResetSecretAndSmtpConfigurationAreComplete`

### 6. Dependencias

- `phpmailer/phpmailer` ^7.1 — instalado
- `firebase/php-jwt` ^7.1 — restaurado

---

## Seguridad

### Antes (vulnerable)
- Tokens reutilizables después del cambio de contraseña
- Secret de email token = secret de access token (mismo valor)
- Expiración de 7 días
- Errores que revelaban si el email existía
- `Mail::send()` retornaba string en error (rompía tipo de retorno)

### Ahora (seguro)
- Tokens de un solo uso (pwdv invalidado al cambiar password)
- Secret dedicado para email tokens (>=32 chars, distinto del access)
- Expiración de 1 hora
- Respuesta neutra: "If the account exists, a password reset link will be sent"
- `Mail::send()` retorna `false` en error (respeta firma `: bool`)

---

## Configuración requerida

### `.env` (ya existente, sin cambios necesarios)

```env
TOKENS_ACCSS_SECRET_KEY='...'   # >=32 chars (access token)
TOKENS_EMAIL_SECRET_KEY='...'   # >=32 chars, DIFERENTE del access
```

### `config/config.php`

```php
'access_token' => [
    'secret_key' => Env::get('TOKENS_ACCSS_SECRET_KEY'),
    'expiration_time' => 60 * 15 * 50000,
    'encryption' => 'HS256',
],
'email_token' => [
    'secret_key' => Env::get('TOKENS_EMAIL_SECRET_KEY'),
    'expires_in' => 3600,  // 1 hora
    'encryption' => 'HS256',
],
```

---

## Testing

```bash
php vendor/bin/phpunit unit-tests/auth/PasswordResetTest.php
# OK (4 tests, 35 assertions)
```

---

## Pendiente

- **SMTP**: La interfaz está lista pero Gmail rechaza las credenciales disponibles. Configurar credenciales SMTP válidas en `.env` para completar el flujo end-to-end.
