# HTTP method override and ACL selection diverge

**Status:** open

**Confidence:** confirmed by focused source-path test

**Severity:** medium; supported method overrides can receive an incorrect 403

## Finding

`Request::method()` honors the configured URL or `X-HTTP-Method-Override` value, and `ApiHandler` dispatches using that resolved method. `ApiController::__construct()` selects ACL callables from raw `$_SERVER['REQUEST_METHOD']` instead.

## Minimal reproduction and evidence

Run:

```powershell
php vendor/bin/phpunit --no-coverage unit-tests/api/ApiAuthorizationDispatchTest.php
```

The test sends raw POST with an override to PATCH and a role `create` grant. The handler resolves `patch`, but the controller adds `post`; exact callable comparison rejects the action. The reverse case (raw PATCH overridden to POST) also builds `patch` while dispatching `post`, and is rejected.

No live method-override request against a DB-backed automatic resource was run. The observed 403 behavior was also confirmed separately against the FrontController's callable gate using a temporary controller with an empty callable list.

## Expected behavior

ACL selection and final dispatch should use the same effective method, or the framework should explicitly reject method overrides for automatic API dispatch.

## Source locations

- `src/framework/Request.php:553-570` — configured override resolution.
- `src/framework/Handlers/ApiHandler.php:62` — resolved method dispatch.
- `src/framework/Api/ApiController.php:120-191` — raw server method used for ACL callable selection.
- `src/framework/FrontController.php:96-100` — exact callable gate.
- `unit-tests/api/ApiAuthorizationDispatchTest.php` — both override directions.
