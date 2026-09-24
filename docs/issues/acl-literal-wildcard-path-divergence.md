# Literal ACL `*` resource grants are reserved

**Status:** RESUELTO

**Confidence:** reserved-syntax contract enforced by the builder and compiler

## Resolution

Literal `*` is not a supported resource wildcard. The compiled `allow['*']` bucket is reserved for the compiler's expansion of explicit `read_all` and `write_all` capabilities. `Acl::addResourcePermissions()` rejects a resource named `*`; both compiler entry points reject direct role snapshots with that resource, and per-user table masks cannot target it. Use `read_all` or `write_all` for global cross-resource grants.

Wildcard denies remain supported and are checked by both compiled lookup paths. A legacy uncompiled literal-star resource entry does not grant another resource.

## Verification

Run:

```powershell
php vendor/bin/phpunit --no-coverage --do-not-cache-result unit-tests/acl/AclWildcardSemanticsTest.php unit-tests/acl/AclCompiledPermissionsTest.php unit-tests/acl/AclEngineDenyTest.php
```

The focused tests verify that the builder rejects literal-star grants, role and user compilation reject direct literal-star grants/masks, legacy lookup does not expand a literal-star entry, and compiled wildcard denies block both permission paths. `AclCompiledPermissionsTest` also verifies the `read_all`/`write_all` sentinel behavior. These tests use in-memory fixtures; no application DB or runtime ACL cache is involved.

## Source locations

- `src/framework/Security/Acl.php` — the public resource-permission builder rejects `*`.
- `src/framework/Security/Compiler/EffectivePermissionCompiler.php` — role and user compilation reserve `*` for explicit global capabilities.
- `src/framework/Security/Engine/AclEngine.php:56-64` — resource-specific lookup checks named allow buckets and wildcard denies.
- `src/framework/Security/Engine/AclEngine.php:287-297` — generic compiled lookup also checks wildcard allows.
- `unit-tests/acl/AclWildcardSemanticsTest.php` — focused behavior tests.
