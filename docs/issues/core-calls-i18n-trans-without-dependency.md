# Core calls `trans()` without declaring `simplerest-i18n`

**Status:** RESUELTO en fuente (2026-11-22, autorizado por el maintainer; pendiente de release del core)

**Confidence:** confirmed by source search on 2026-11-22 (core `v3.0.5`, source `abf52c218`)

## Problem

The microcore (`src/framework`) calls the global function `trans()`, but no file under `src/framework` defines it. The function is defined by `boctulus/simplerest-i18n` (`src/helpers.php`), which the core manifest does not require. In a consumer that installs only `boctulus/simplerest`, these paths end in a fatal `Call to undefined function trans()` instead of the intended HTTP error:

- `src/framework/Api/ApiController.php` — validation errors (4 calls).
- `src/framework/Api/Collections.php` — unavailable collections and validation errors.
- `src/framework/Handlers/MiddlewareHandler.php` — missing middleware.
- `src/framework/Libs/HTML.php` — generates view code that calls `trans()`.

`unit-tests/microcore` checks that classes load without packages; it does not detect calls to functions that the core does not define.

## Current mitigation

`boctulus/simplerest-cli` requires `boctulus/simplerest-i18n`, and the generated `app.php` initializes it, so applications bootstrapped with `vendor/bin/simplerest init` define `trans()`. A project that uses the core without the CLI is still affected.

## Resolution

Option 1 was applied together with option 3:
- `src/framework/Helpers/trans.php` defines `trans()` only when it does not exist yet, and returns the text unchanged. `simplerest-i18n` loads its own `trans()` through Composer's autoload `files`, which run before the core helpers, so the package version wins whenever it is installed.
- `Libs/Validator.php` calls `Translate::bind()` only when `class_exists(Translate::class)`.
- `unit-tests/microcore/CoreReferencesResolveTest.php` now fails when the core calls a function that neither PHP nor the core defines.

## Options considered

1. Define a minimal `trans()` in the core helpers that returns the text unchanged when `simplerest-i18n` is absent. This contradicts the "no fallbacks without approval" rule unless it is approved explicitly.
2. Replace the calls in the core with plain strings or `SystemMessages`, and keep translation in the package.
3. Extend `unit-tests/microcore` to reject calls to functions that are not defined in `src/framework`.

Any fix changes the core and requires a new core release.
