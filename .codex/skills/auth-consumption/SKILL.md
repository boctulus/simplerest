---
name: auth-consumption
description: Guides how to consume authentication endpoints (login, register, refresh, logout, password reset, email verification) and use the auth() helper in SimpleRest.
---

# Auth Consumption Skill

Guides how to interact with the JWT dual-token authentication system from both **backend code** and **frontend clients**.

## Available Endpoints

| Method | Route | Description |
|--------|-------|-------------|
| POST | `/auth/register` | User registration |
| POST | `/auth/login` | Login (returns access + refresh tokens) |
| POST | `/auth/logout` | Invalidate session |
| POST | `/auth/refresh` | Refresh access token |
| POST | `/auth/password-reset` | Request password reset |
| POST | `/auth/password-reset/confirm` | Confirm password reset |
| POST | `/auth/verify-email` | Verify email address |
| GET | `/auth/me` | Current user data |
| GET | `/api/v1/auth/me` | Current user data (API) |

All endpoints also available under `/api/v1/auth/*`.

## Login Flow

```bash
curl -X POST /auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"secret"}'
```

Response:
```json
{
  "access_token": "eyJ...",
  "refresh_token": "eyJ...",
  "expires_in": 3600,
  "token_type": "Bearer"
}
```

## Using Tokens in Requests

```bash
curl -H "Authorization: Bearer eyJ..." /api/v1/products
```

## Refresh Token Flow

```bash
curl -X POST /auth/refresh \
  -H "Content-Type: application/json" \
  -d '{"refresh_token": "eyJ..."}'
```

## Registration

```bash
curl -X POST /auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John","email":"john@test.com","password":"secure123","password_confirmation":"secure123"}'
```

## Using auth() Helper in Backend

```php
// Get authenticated user
$userId = auth()::getCurrentUserId();
$user   = auth()::getCurrentUser();

// Check authentication status
if (auth()::isAuthenticated()) {
    // user is logged in
}

// Get the current JWT token
$token = Request::getInstance()->bearerToken();

// Get user role
$role = auth()::getCurrentUserRole();
```

## Password Reset

Step 1 — Request reset:
```bash
curl -X POST /auth/password-reset \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com"}'
```

Step 2 — Confirm with token:
```bash
curl -X POST /auth/password-reset/confirm \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","token":"reset_token","password":"new_pass","password_confirmation":"new_pass"}'
```

## Token Structure

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

## Configuration

In `config/config.php`:

```php
'access_token' => [
    'secret_key'      => env('JWT_SECRET'),
    'expiration_time'  => 3600,        // 1 hour
    'encryption'       => 'HS256',
],
'refresh_token' => [
    'secret_key'      => env('JWT_REFRESH_SECRET'),
    'expiration_time'  => 1209600,      // 14 days
    'encryption'       => 'HS256',
],
'remember_me_token' => [
    'secret_key'      => env('JWT_REMEMBER_SECRET'),
    'expiration_time'  => 2592000,      // 30 days
    'encryption'       => 'HS256',
],
```

## Frontend Integration Tips

- Store `access_token` in memory (not localStorage) for security
- Store `refresh_token` in httpOnly cookie if possible
- Intercept 401 responses to trigger `/auth/refresh` automatically
- On refresh failure, redirect to login

## Custom User Table

Configurable in `config/config.php`:

```php
'users_table'           => 'users',
'users_password_field'  => 'password',
'users_email_field'     => 'email',
'users_username_field'  => 'username',
```

## Social Auth (OAuth)

Pre-configured for Google and Facebook. Enable in config and redirect to the appropriate OAuth URL.

## See Also

- [`docs/Authentication.md`](../docs/Authentication.md) — auth architecture
- [`docs/ACL.md`](../docs/ACL.md) — permissions post-authentication
- `security-hardening` skill — JWT config hardening
- `acl-operations` skill — managing user roles & permissions
