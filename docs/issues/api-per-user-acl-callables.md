# Per-user ACL mask does not reliably control API callables

**Status:** open

**Confidence:** confirmed by focused source-path tests

**Severity:** high; can deny a per-user grant or preserve a role grant despite a per-user mask

## Finding

`ApiController::__construct()` first adds callables from role permissions, then processes a non-null per-user table-permission mask. That second pass appends to `$this->callable`; it does not clear the role-derived methods although the nearby comment says the per-user mask replaces role permissions.

The bitmask branch currently maps POST's `create` bit to `get`, which does not match `ApiHandler`'s `post` dispatch. During this audit the PATCH mapping was narrowly corrected from `putch` to `patch`, matching the dispatched method. A role grant can still mask the POST denial by having already added `post`; conversely, a zero per-user mask does not remove a role callable.

This issue remains open for the POST mapping and the per-user replacement behavior after the narrow PATCH token correction.

## Minimal reproduction and evidence

Run:

```powershell
php vendor/bin/phpunit --no-coverage unit-tests/api/ApiAuthorizationDispatchTest.php
```

The isolated probe uses the real `ApiController` constructor and `ApiHandler::resolve()` with fake auth, ACL, request, and model providers. It observed:

- Original POST, `create` bit only (`4`), no role grant: dispatch is `post`; callables contain `get`, not `post`.
- Original PATCH, `update` bit only (`2`), no role grant: before the correction, dispatch was `patch` while callables contained `putch`; after the correction, the focused test confirms `patch` is callable and dispatch is allowed.
- Original POST, role `create` grant plus a non-null per-user mask of `0`: `post` remained callable.

The FrontController checks exact membership in `getCallable()` before calling the action. A separate local HTTP request with an empty callable list returned 403, confirming the gate's wire status. No live DB-backed user-permission request was run.

## History and intentionality

In the pre-merge `app/core/api/v1/ApiController.php`, the operation map associated `create` with `post` and `update` with `put` and `patch`. The 2020 merge commit `08ab30db0c` added the per-user mask branch with the `get` and `putch` tokens. Repository-wide search found no other caller of `putch` or contract relying on `get` for POST. No test covering these exact cases existed before this audit.

That evidence supports treating both spellings as probable mapping errors, not as established compatibility behavior. The PATCH token was corrected after the focused reproduction. The POST token and the mask's failure to replace role-derived callables remain open findings.

## Expected behavior

For a non-null per-user mask, the final callable set should reflect that mask according to the nearby source comment's replacement semantics. Its create/update bits should resolve to the actual dispatched tokens `post` and `put`/`patch`. Current tests confirm that PATCH now resolves to `patch`; POST still resolves to `get`, and role-derived callables currently survive a zero mask.

## Source locations

- `src/framework/Api/ApiController.php:120-182` — role-derived callable construction and mask lookup.
- `src/framework/Api/ApiController.php:191-239` — per-user bitmask branch; POST and PATCH mappings.
- `src/framework/Handlers/ApiHandler.php:62` — dispatch method comes from `Request::method()`.
- `src/framework/FrontController.php:96-100` — exact callable membership gate.
- `unit-tests/api/ApiAuthorizationDispatchTest.php` — focused constructor and dispatch regression cases.
