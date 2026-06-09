---
name: security-hardening
description: Security best practices for SimpleRest including JWT configuration, ACL hardening, input sanitization, CSRF protection, and encryption.
---

# Security Hardening Skill

## JWT Configuration (`config/config.php`)

```php
'access_token' => [
    'secret_key'      => env('JWT_SECRET'),           // MUST be set
    'expiration_time' => 3600,                        // 1 hour
    'encryption'      => 'HS256',
],
'refresh_token' => [
    'secret_key'      => env('JWT_REFRESH_SECRET'),   // DIFFERENT from access
    'expiration_time' => 1209600,                     // 14 days
],
'remember_me_token' => [
    'secret_key'      => env('JWT_REMEMBER_SECRET'),  // DIFFERENT key
    'expiration_time' => 2592000,                     // 30 days
],
```

### JWT Rules

- Different secrets for each token type
- Short access TTL (15-60 min), refresh up to 14 days
- Store in `.env`, never in code
- Generate secrets: `openssl rand -hex 64`

### Auth Checks

```php
$userId = auth()::getCurrentUserId();
$user   = auth()::getCurrentUser();
if (!auth()::isAuthenticated()) { /* 401 */ }
$token = Request::getInstance()->bearerToken();
```

## ACL Hardening

```php
// config/acl.php — order matters!
$acl->addRole('guest', 10);
$acl->addRole('registered', 20)->addInherit('guest');    // inherit FIRST
$acl->addRole('admin', 100)->addInherit('registered');
$acl->addResourcePermissions('products', ['show', 'list'], 'guest');
$acl->addResourcePermissions('products', ['create'], 'registered');
$acl->addSpecialPermissions(['read_all', 'write_all'], 'admin');
```

```bash
php com make acl --force            # apply changes
php com make acl --force --debug    # preview
```

### ACL Dont's

- `addInherit()` AFTER permissions -> error
- Dont grant `write_all` to non-admin roles
- Dont grant `fill_all` casually (bypasses fillable protection)
- Dont grant `impersonate` except to super-admins

## Input Sanitization

```php
Strings::sanitize($input, true, true, 'a-z0-9-');
Strings::slug($name);
Arrays::sanitizeArrayKeys($data);
```

Use Validation over manual sanitization when possible.

## Encryption

```php
use Boctulus\Simplerest\Libs\SimpleCrypt;
$encrypted = SimpleCrypt::encrypt($sensitiveData);
$decrypted = SimpleCrypt::decrypt($encrypted);
```

Use for: API keys in DB, OAuth tokens, PII.

## Cookie Security

```php
Cookie::set('session', $token, 3600, '/', '', true, true);  // secure + httponly
```

## Security Checklist

- [ ] Different JWT secrets for access/refresh/remember-me
- [ ] Access token TTL <= 1 hour
- [ ] ACL regenerated after any config/acl.php change
- [ ] Input validated before DB operations
- [ ] .env excluded from version control
- [ ] Debug mode OFF in production
- [ ] `$hidden` set on models for passwords/tokens
- [ ] QB uses prepared statements (SQL injection safe)

## Common Threats

| Threat | Mitigation |
|--------|-----------|
| SQL Injection | QB prepared statements. Never concatenate in DB::statement() |
| JWT Theft | Short TTL, HTTPS only, different secrets |
| Privilege Escalation | Test ACL with --debug, check inheritance |
| Mass Assignment | `$not_fillable` or `unfill()` on models |
| XSS | Output escaping, Strings::sanitize() for user input |
