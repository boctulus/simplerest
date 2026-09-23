# Automatic API controller references missing impersonation types

**Status:** open

**Confidence:** high source-level finding; full authorized-resource HTTP reproduction not performed

**Severity:** high if no external runtime loader supplies these classes

## Finding

The built-in `ApiController` constructor unconditionally calls `ImpersonationRequestContext::hydrateFromToken()` and `ImpersonationManager::getInstance()->enforceReadOnly()`. Neither class definition exists in the repository, and Composer autoload reports both classes absent. A request that passes auth and reaches this point in a built-in resource controller is expected to fail with a class-not-found error before the action executes.

This finding is directly relevant to runtime authorization probes: invalid credentials return before this code, while an authorized request is required to reach it. The 403 runtime probe therefore used a temporary plain controller to exercise the FrontController callable gate without entering this broken constructor path.

## Evidence

```powershell
php -r 'require "vendor/autoload.php"; var_dump(class_exists("Boctulus\\Simplerest\\Core\\Libs\\Impersonation\\ImpersonationRequestContext")); var_dump(class_exists("Boctulus\\Simplerest\\Core\\Libs\\Impersonation\\ImpersonationManager"));'
```

Both results were `bool(false)`. Repository search found the two imports and calls but no class definitions. The focused API-constructor unit fixture stubs these optional types solely to reach and test ACL callable construction; it does not establish production availability.

## Expected behavior

Either provide and autoload the required impersonation types, or make the integration optional with an explicit availability/configuration check before invoking it.

## Source locations

- `src/framework/Api/ApiController.php:15-16` — imports.
- `src/framework/Api/ApiController.php:257-263` — unconditional constructor calls.
- `unit-tests/api/fixtures/api_authorization_dispatch_probe.php` — test-only stubs; not production implementation.
