# Literal ACL `*` grant differs by lookup path

**Status:** open

**Confidence:** confirmed engine behavior; intended contract unresolved

**Severity:** medium if literal `*` grants are intended to be resource wildcards

## Finding

With compiled permissions, a literal resource grant under `*` makes generic `AclEngine::can($context, 'show', 'products')` return true. `AclEngine::hasResourcePermission('show', 'products', $context)` returns false for the same compiled context. The legacy uncompiled context returns false through both paths. Wildcard denies are checked by both compiled paths.

The automatic API controller uses the resource-specific lookup. `read_all`/`write_all` capabilities are a separate mechanism and are not evidence that arbitrary literal `*` rules are an intended public contract.

## Minimal reproduction and evidence

Run:

```powershell
php vendor/bin/phpunit --no-coverage unit-tests/acl/AclWildcardSemanticsTest.php
```

Three tests assert compiled literal-star divergence, legacy-path behavior, and compiled wildcard-deny behavior. They exercise the actual `AclEngine` and compiler with an in-memory snapshot; no application DB or runtime ACL cache is involved.

## Expected behavior

The framework owner should decide whether `*` is a supported literal resource wildcard. If it is, generic and resource-specific lookup should agree. If it is not, the compiler/builder should reject or document it as reserved syntax.

## Source locations

- `src/framework/Security/Compiler/EffectivePermissionCompiler.php:132-139` — special read/write capabilities compile to the wildcard bucket.
- `src/framework/Security/Engine/AclEngine.php:56-64` — resource-specific lookup checks named allow buckets and wildcard denies.
- `src/framework/Security/Engine/AclEngine.php:287-297` — generic compiled lookup also checks wildcard allows.
- `unit-tests/acl/AclWildcardSemanticsTest.php` — focused behavior tests.
