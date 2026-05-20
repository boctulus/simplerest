---
name: security-hardening
description: Security best practices for SimpleRest including JWT configuration, ACL hardening, input sanitization, CSRF protection, and encryption.
---

# Security Hardening Skill

## JWT Authentication

### Configuration (`config/config.php`)

```php
'access_token' => [
    'secret_key'      => env('JWT_SECRET'),          // MUST be set
    'expiration_time' => 3600,                       // 1 hour
    'encryption'      => 'HS256',
],
'refresh_token' => [
    'secret_key'      => env('JWT_REFRESH_SECRET'),  // DIFFERENT from access
    'expiration_time' => 1209600,                    // 14 days
    'encryption'      => 'HS256',
],
'remember_me_token' => [
    'secret_key'      => env('JWT_REMEMBER_SECRET'), // DIFFERENT key
    'expiration_time' => 2592000,                    // 30 days
    'encryption'      => 'HS256',
],
```

### Security Rules for JWT

- **Use different secrets** for access, refresh, and remember-me tokens
- **Short access token TTL** (15-60 min), longer refresh (14 days max)
- **Store secrets in `.env`**, never in code
- **Validate on every request** — don't trust cached JWT validity

### Checking Auth in Controllers

```php
$userId = auth()::getCurrentUserId();
$user   = auth()::getCurrentUser();

if (!auth()::isAuthenticated()) {
    // 401
}

// Bearer token from request
$token = Request::getInstance()->bearerToken();
```

### Auth Endpoints

```
POST /auth/register
POST /auth/login
POST /auth/logout
POST /auth/refresh
POST /auth/password-reset
POST /auth/password-reset/confirm
POST /auth/verify-email
GET  /auth/me
```

## ACL Hardening

### Principle of Least Privilege

```php
// config/acl.php

// 1. Define roles with inheritance FIRST
$acl->addRole('guest', 10);
$acl->addRole('registered', 20)->addInherit('guest');
$acl->addRole('editor', 30)->addInherit('registered');
$acl->addRole('admin', 100)->addInherit('editor');

// 2. Grant minimal permissions per role
$acl->addResourcePermissions('products', ['show', 'list'], 'guest');
$acl->addResourcePermissions('products', ['create', 'update'], 'registered');
$acl->addResourcePermissions('products', ['delete'], 'editor');

// 3. Special permissions — VERY carefully
$acl->addSpecialPermissions(['read_all', 'write_all'], 'admin');  // only admin
```

### ACL Regeneration

```bash
php com make acl --force           # apply changes
php com make acl --force --debug   # preview before applying
```

### Common Mistakes

1. **Call `addInherit()` BEFORE adding permissions** — inheritance must be declared first
2. **Don't grant `write_all` to non-admin roles** — allows modifying others' records
3. **Don't grant `fill_all` casually** — bypasses fillable field protection
4. **Don't grant `impersonate`** except to super-admins

## Input Sanitization

### Strings

```php
use Boctulus\Simplerest\Libs\Strings;

// Sanitize: remove unwanted chars
$clean = Strings::sanitize($input, true, true, 'a-z0-9-');

// Slugify for URLs
$slug = Strings::slug($name);
```

### Arrays

```php
use Boctulus\Simplerest\Libs\Arrays;

// Sanitize all array keys
$clean = Arrays::sanitizeArrayKeys($data);
```

### Validation (preferred over manual sanitization)

```php
$validator->validate([
    'email' => 'required|email',
    'age'   => 'required|integer|min:18',
], $_POST);
```

## CSRF Awareness

The framework can check for CSRF tokens (if using views/forms):

```php
// Include CSRF token in forms
<?php echo csrf_field(); ?>
```

## Encryption (SimpleCrypt)

```php
use Boctulus\Simplerest\Libs\SimpleCrypt;

$encrypted = SimpleCrypt::encrypt($sensitiveData);
$decrypted = SimpleCrypt::decrypt($encrypted);
```

Use for:
- API keys stored in DB
- OAuth tokens
- PII (Personally Identifiable Information)

## Cookie Security

```php
use Boctulus\Simplerest\Libs\Cookie;

Cookie::set('session', $token, 3600, '/', '', true, true);
// secure=true, httponly=true — prevents JS access and HTTPS-only
```

## Security Checklist

- [ ] `JWT_SECRET`, `JWT_REFRESH_SECRET`, `JWT_REMEMBER_SECRET` set in `.env`
- [ ] Different secrets for each token type (not the same key!)
- [ ] Access token TTL ≤ 1 hour
- [ ] Refresh token TTL ≤ 14 days
- [ ] ACL regenerated after any `config/acl.php` change
- [ ] Input validated before database operations (not just sanitized)
- [ ] `tb_prefix` not exposing table structure info
- [ ] .env excluded from version control
- [ ] Debug mode disabled in production
- [ ] SQL injection prevented via prepared statements (QB does this automatically)
- [ ] Hidden fields (`$hidden`) set on models for sensitive columns

## Common Vulnerabilities & Mitigations

| Threat | Mitigation |
|--------|-----------|
| SQL Injection | QB uses prepared statements. Don't use `DB::statement()` with concatenated user input |
| JWT Theft | Short TTL, HTTPS only, different secrets per token type |
| Privilege Escalation | Test ACL with `--debug`, verify role inheritance chain |
| Mass Assignment | Use `$not_fillable` or `unfill()` to protect fields |
| XSS | Output escaping in views, use `Strings::sanitize()` for user input |
| CSRF | CSRF tokens on state-changing requests in web routes |
| Sensitive Data Exposure | Use `$hidden` on models, encrypt PII with SimpleCrypt |
| Weak JWT Secret | Use `openssl rand -hex 64` to generate secrets |
