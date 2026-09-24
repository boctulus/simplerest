# Per-user ACL mask does not reliably control API callables

**Status:** implemented; focused regression passed

**Confidence:** source behavior and the isolated regression probe passed; no live DB-backed per-user permission request was run

**Severity:** resolved in the focused controller path; live DB-backed authorization remains unverified

## Finding

`ApiController` now reads the per-user table-permission mask before selecting callables. A non-null mask controls resource callables, GET list/retrieve flags, and the `list_all`/`show_all` owner-scope bypass decisions instead of being added to role-derived resource permissions. The global `read_all` and `write_all` capabilities continue to apply, matching the ACL engine's global-capability behavior.

The `create` bit now adds the `post` callable. PUT and PATCH use the same `update` bit and add their dispatched callable token.

## Minimal reproduction and evidence

Run:

```powershell
php vendor/bin/phpunit --no-coverage unit-tests/api/ApiAuthorizationDispatchTest.php
```

The isolated probe uses the real `ApiController` constructor and `ApiHandler::resolve()` with fake auth, ACL, request, and model providers. The earlier characterization observed:

- Before the fix, original POST with the `create` bit only (`4`) and no role grant dispatched as `post`, while callables contained `get`, not `post`.
- Original PATCH, `update` bit only (`2`), no role grant: before the correction, dispatch was `patch` while callables contained `putch`; after the correction, the focused test confirms `patch` is callable and dispatch is allowed.
- Original POST, role `create` grant plus a non-null per-user mask of `0`: `post` remained callable before the fix.

The focused run passed with 9 tests and 76 assertions. It covers the `create` → `post`, `update` → `put`/`patch`, and `delete` mappings; confirms a zero user mask suppresses role resource grants; checks that GET `list` and `show` bits set their flags independently; exercises the `list_all`/`show_all` decisions used by owner scoping; and confirms global `read_all`/`write_all` remain effective with a user mask.

The owner-scope helper decisions are covered, but the probe does not execute a real query or load per-user permissions from a database. The previously recorded local HTTP 403 check for an empty callable list was not repeated during this run.

The FrontController checks exact membership in `getCallable()` before calling the action. A separate local HTTP request with an empty callable list returned 403, confirming the gate's wire status. No live DB-backed user-permission request was run.

## History and intentionality

In the pre-merge `app/core/api/v1/ApiController.php`, the operation map associated `create` with `post` and `update` with `put` and `patch`. The 2020 merge commit `08ab30db0c` added the per-user mask branch with the `get` and `putch` tokens. Repository-wide search found no other caller of `putch` or contract relying on `get` for POST. No test covering these exact cases existed before this audit.

That evidence supported correcting both callable tokens and applying a non-null table mask in place of role-derived resource callables. The focused regression now confirms POST, PATCH, zero-mask replacement, and global-capability behavior. Database-backed permission loading and effects remain unverified.

## Expected behavior

For a non-null per-user mask, resource callables, GET list/retrieve flags, and owner-scope bypass follow that mask, while global `read_all` and `write_all` capabilities remain effective. Create/update bits resolve to the actual dispatched tokens `post` and `put`/`patch`.

## Source locations

- `src/framework/Api/ApiController.php:120-222` — effective method, mask-selected callables, and impersonation guard.
- `src/framework/Api/ApiController.php:272-285`, `513-530`, and `861-872` — per-user `list_all`/`show_all` mask checks for owner scoping.
- `src/framework/Handlers/ApiHandler.php:62` — dispatch method comes from `Request::method()`.
- `src/framework/FrontController.php:96-100` — exact callable membership gate.
- `unit-tests/api/ApiAuthorizationDispatchTest.php` — focused constructor and dispatch regression cases.
