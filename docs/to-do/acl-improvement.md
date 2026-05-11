# ACL Bugfixes (Phase 0 — Pre-deny-semantics)

## Background

The ACL system had two design-level bugs that rendered the entire user-level
override layer (`user_tb_permissions` / `user_sp_permissions`) non-functional.
Both were fixed before proceeding to the deny-semantics evolution.

---

## Bug 1 — `setPermissions()` never called in `AuthController::check()`

### Root cause

The `AuthController::check()` method calls `static::setRoles()` in every
authentication branch (API Key, JWT, guest/default), but never calls
`static::setPermissions()`. The JWT payload contains the `permissions` key
(populated at login via `fetchPermissions()`), but the data is never written
into `static::$current_user_permissions`.

### Effect

`auth()->getPermissions()` always returns `[]`, making every downstream consumer
believe there are no user-level overrides:

- `Acl::hasSpecialPermission()` — never sees `user_sp_permissions`
- `Acl::hasPermission()` — never sees `user_tb_permissions`
- `Acl::getTbPermissions()` / `getSpPermissions()` — always return `null`
- `ApiController::__construct()` — the `$perms !== NULL` replacement block
  never executes, so the **replacement semantics for `user_tb_permissions` has
  never actually worked**

### Files changed

| File | Line | Change |
|------|------|--------|
| `src/framework/Api/AuthController.php` | 846 | Added `static::setPermissions($perms)` for API Key branch |
| `src/framework/Api/AuthController.php` | 869 | Added `static::setPermissions($ret['permissions'] ?? [])` for JWT branch |
| `src/framework/Api/AuthController.php` | 894 | Added `static::setPermissions($perms)` for guest/default branch |

### Bonus fix

Line 846 had `static::setRoles($ret['roles'])` where `$ret` is the static cache
variable, still `null` on first call. Changed to `static::setRoles($roles)`.

---

## Bug 2 — Wrong key access in `auth()->getPermissions()` usage

### Root cause

`auth()->getPermissions()` returns `['tb' => [...], 'sp' => [...]]` (a
two-key associative array). Many callers treated it as a flat array:

```php
// Before (wrong)
$userSpPerms = auth()->getPermissions() ?? [];
$userTbPerms = auth()->getPermissions() ?? [];
```

This made `$userSpPerms` an `['tb'=>..., 'sp'=>...]` array instead of a flat
list of special-permission names. `in_array()` checks against it always failed.

### Files changed

| File | Line | Change |
|------|------|--------|
| `src/framework/Acl.php` | 311 | `auth()->getPermissions()['sp'] ?? []` |
| `src/framework/Acl.php` | 370 | Extract `['sp']` + `['tb']` separately |
| `src/framework/Acl.php` | 572, 585, 603 | Extract `['sp']` + `['tb']` in convenience wrappers |

---

## Bug 3 — `AclContext::$userTbPerms` declared but never used

### Root cause

`AclContext` defines `$userTbPerms` (line 12) but:

1. `PermissionEvaluator::hasPermission()` never checks it
2. No caller ever populates it (not even `Acl::hasPermission()`)

### Effect

The entire `user_tb_permissions` override mechanism was dead code at the
evaluation layer.

### Files changed

| File | Line | Change |
|------|------|--------|
| `src/framework/Security/Engine/PermissionEvaluator.php` | 72-94 | Added user-level `userTbPerms` check between special-permission short-circuit and role-level fallback |
| `src/framework/Acl.php` | 370-378 | Pass `userTbPerms` to `AclContext` in `hasPermission()` |
| `src/framework/Acl.php` | 572-604 | Pass `userTbPerms` to `AclContext` in convenience wrappers |

### Evaluation order (after fix)

```
1. read_all / write_all special perms       → short-circuit grant
2. user_tb_permissions override (packed)     → replacement if present
3. role-level tb_permissions from snapshot   → fallback
```

---

## Test results

```text
$ php vendor/bin/phpunit unit-tests/acl/AclEngineTest.php unit-tests/acl/RoleHierarchyServiceTest.php
OK (42 tests, 45 assertions)
```

*(Psr7AdaptersTest errors are pre-existing — unrelated `Request::setInstance()` missing)*

---

## Next step: deny-semantics evolution

The override layer now works. The foundation is ready to evolve from
replacement-based revocation to explicit ALLOW/DENY policy rules.
See `docs/issues/acl-deny-semantics.md` (pending).
