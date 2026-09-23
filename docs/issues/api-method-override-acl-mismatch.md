# HTTP method override and ACL selection diverge

**Status:** resolved in source; focused regression passed

**Confidence:** header and URL method overrides passed the focused regression in the current worktree

**Severity:** medium; supported method overrides can receive an incorrect 403

## Finding

`ApiController::__construct()` now selects ACL callables and invokes the read-only impersonation guard with the effective method from `Request::method()`, matching `ApiHandler` dispatch. `Request::method()` caches the resolved method for the request so a URL `_method` override remains available after the first lookup consumes it from query parameters.

## Minimal reproduction and evidence

Run:

```powershell
php vendor/bin/phpunit --no-coverage unit-tests/api/ApiAuthorizationDispatchTest.php
```

The earlier test sent raw POST with an override to PATCH and a role `create` grant. The handler resolved `patch`, but the controller added `post`; exact callable comparison rejected the action. The reverse case (raw PATCH overridden to POST) also built `patch` while dispatching `post`, and was rejected.

The regression fixture checks a header override and a URL `_method` override through the real `ApiController` constructor and `ApiHandler::resolve()`. The focused command passed: 5 tests and 31 assertions. Its expectations require ACL selection and dispatch to use the same method.

No live method-override request against a DB-backed automatic resource was run. The observed 403 behavior was also confirmed separately against the FrontController's callable gate using a temporary controller with an empty callable list.

## Expected behavior

ACL selection, read-only impersonation enforcement, and final dispatch use the same effective method, including both configured override sources.

## Source locations

- `src/framework/Request.php:559-589` — cached configured override resolution.
- `src/framework/Handlers/ApiHandler.php:62` — resolved method dispatch.
- `src/framework/Api/ApiController.php:120-220` — effective method used for ACL callable selection and impersonation guard.
- `src/framework/FrontController.php:96-100` — exact callable gate.
- `unit-tests/api/ApiAuthorizationDispatchTest.php` — both override directions.
