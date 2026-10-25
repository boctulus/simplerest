# Configuration and environment values

This page separates the framework's in-memory `Config` API from the environment-file helpers. Bootstrap uses both paths, and their lookup behavior is not identical.

## In-memory configuration

`Config::get()` lazily includes `config/config.php` and `config/databases.php`, merges those arrays, and stores the result in static memory for the process. `Config::get('base_url', $default)` accepts dot-separated paths; `Config::set()` writes a dot path, and `Config::add()` appends to an array at a path. The root merge keys accepted by the current `string $property` signature are `''`, `'/'`, and `'.'`.

`loadPackageConfig($vendor, $package, $array)` stores values under `packages.{vendor}.{package}`. `getPackageConfig()` checks that package path first and falls back to the global key. `config($key)` is a wrapper around `Config::get()`. `set_cfg($key, $value)` delegates to `Config::set()`.

`get_cfg($key)` has a second behavior: when `$key` ends in `.php`, it includes that file from `CONFIG_PATH`; otherwise it reads the in-memory configuration. For example, the CORS helper uses `get_cfg('cors.php')` to load the PHP file directly.

## Environment loading

### Architectural decision: layered environment resolution

`Env` is the framework's configuration resolver. It parses the configured files and keeps their merged values in memory; loading an overlay never copies values into or overwrites `.env` or `.dev-env`.

The precedence, from lowest to highest, is:

1. `.env` provides the base values.
2. `.dev-env` overlays matching keys and adds new keys only when the effective `APP_ENV` is `local` or `development`. The effective value comes from `getenv('APP_ENV')` when present, including when it is empty; otherwise it comes from `.env`. A missing or empty `APP_ENV`, `production`, and other environment names do not enable `.dev-env`. An applied `.dev-env` must not define `APP_ENV`; `Env::setup()` raises an exception if it does.
3. Process environment values returned by `getenv()` override both file layers during keyed reads. A present empty process value also wins.

The `.dev-env` file is for local overrides and is ignored by Git. Its presence cannot select development mode by itself. In production, or when `APP_ENV` is not `local` or `development`, the file is ignored. The current `.env.example` uses `APP_ENV=local`.

`Env::setup()` uses `.env` as its base and applies the eligible `.dev-env` overlay. To replace the file layers explicitly, call `Env::setup(file: '.env.testing', inherit: false)`. This loads only the named file at the file layer; process environment values still have the highest precedence, so `inherit: false` does not isolate a test from process variables. An optional `required` list is validated after file resolution, and a required key that is absent or empty raises an exception. The framework does not assume a global list of required keys because applications have different requirements.

An empty value is present: it overrides lower layers and `Env::get('KEY', 'fallback')` returns `''`. `Env::getBool()` interprets an empty value as `false`; a missing key uses the supplied default. Every `Env::setup()` call replaces the prior in-memory file data so values from earlier initializations do not carry over. If setup fails, `Env` keeps no prior data and later reads rethrow that setup error until another explicit `Env::setup()` succeeds.

`Env` and the global `env($key, $default)` helper are the canonical configuration reads; keyed calls check `getenv()` first, and calling `Env::get()` without a key returns the in-memory file layers without copying every ambient process variable into that map. For compatibility, `app.php` still lets optional `Dotenv\Dotenv` load `.env` into `$_ENV` and `$_SERVER`. Those superglobals reflect the base file only and can differ from the in-memory overlay; application configuration should not read them directly.

`Env::setup()` does not create `.env` as a side effect of reading configuration. The Composer `post-root-package-install` script creates a sanitized `.env` from `.env.example` during `create-project`; from an existing checkout, run `composer run-script post-root-package-install` explicitly when `.env` is absent. A plain `composer install` does not call this event. The [core runtime findings](../audit/README.md#core-runtime-and-http-audit-2026-10-24) record the earlier loader behavior and filename mismatch.

`Env::parse()` handles full-line `#` and `;` comments, optional `export `, BOM and common line endings, bare keys, empty values, and quoted values. For unquoted values it treats `#` or `;` as an inline comment only when preceded by whitespace. Double quotes recognize `\"` and `\\`; other backslash sequences are preserved. Section headers are ignored.
