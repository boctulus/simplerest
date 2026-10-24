# Configuration and environment values

This page separates the framework's in-memory `Config` API from the environment-file helpers. Bootstrap uses both paths, and their lookup behavior is not identical.

## In-memory configuration

`Config::get()` lazily includes `config/config.php` and `config/databases.php`, merges those arrays, and stores the result in static memory for the process. `Config::get('base_url', $default)` accepts dot-separated paths; `Config::set()` writes a dot path, and `Config::add()` appends to an array at a path. The root merge keys accepted by the current `string $property` signature are `''`, `'/'`, and `'.'`.

`loadPackageConfig($vendor, $package, $array)` stores values under `packages.{vendor}.{package}`. `getPackageConfig()` checks that package path first and falls back to the global key. `config($key)` is a wrapper around `Config::get()`. `set_cfg($key, $value)` delegates to `Config::set()`.

`get_cfg($key)` has a second behavior: when `$key` ends in `.php`, it includes that file from `CONFIG_PATH`; otherwise it reads the in-memory configuration. For example, the CORS helper uses `get_cfg('cors.php')` to load the PHP file directly.

## Environment loading

When `app.php` finds the optional `Dotenv\Dotenv` class, it loads `.env` with `createImmutable(__DIR__)`. The framework's custom `Env` class is separate: its data is loaded lazily when `Env::get()` or `Env::getBool()` first runs. `config/config.php` calls those methods while building the configuration array.

The custom `Env::setup()` selects a non-empty `.dev-env` before `.env`. If `.env` is absent, it looks for a file named `env.example` and copies it to `.env`; the tracked template is named `.env.example`, so this fallback does not match that template name. It then parses the selected file into static data. Do not treat calling `Env::get()` as a read-only operation on a checkout that has no `.env`.

The global `env($key, $default)` helper first checks `getenv($key)`. If the process value is present, including an empty string, it returns that value; otherwise it delegates to `Env::get()`. A direct `Env::get()` reads only the custom loader's static file data and does not perform that process lookup.

`Env::parse()` handles full-line `#` and `;` comments, optional `export `, BOM and common line endings, bare keys, empty values, and quoted values. For unquoted values it treats `#` or `;` as an inline comment only when preceded by whitespace. Double quotes recognize `\"` and `\\`; other backslash sequences are preserved. Section headers are ignored.

## Evidence and limits

`EnvParserTest.php` and `EnvHelperPrecedenceTest.php` contain focused assertions for parsing and process-over-file precedence; they were inspected, not run. Bootstrap, environment-file loading, and process-level precedence were not reproduced in this audit. See the [core runtime findings](../audit/README.md#core-runtime-and-http-audit-2026-10-24) for the root-key signature and fallback filename limits.
