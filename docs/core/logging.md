# Logging

The framework exposes `Logger` plus the global helpers `logger()`, `log_error()`, and `log_sql()`. The helpers have different configuration gates; direct calls to `Logger` do not all follow those helper gates.

## Global helpers and gates

| Helper | Gate | Target |
| --- | --- | --- |
| `logger($data, $path, $append)` | `debug` | General log |
| `log_error($error)` | `log_errors` | Error log |
| `log_sql($sql)` | both `debug` and `log_sql` | SQL log |

`Logger::log()`, `Logger::logError()`, and `Logger::logSQL()` can be called directly. In particular, `ErrorHandler` calls `Logger::logError()` and `Logger::log()` directly, so `log_errors=false` is not a global suppression switch for every error path.

## Logger output

The source declares default filenames `log.txt`, `errors.txt`, and `sql_log.txt`. `Logger::_log()` adds a timestamp by default and writes through PHP `error_log()` when the framework config key `error_log` is true; when false, it calls `Files::writeOrFail()`.

Two source-level filename overrides are unreliable in this checkout: `getLogFilename()` begins with a non-null `log.txt`, so the configured `log_file` is not read on the default path; `setSqlfilename()` assigns the error filename property while `logSQL()` writes the SQL filename property. See the [runtime audit](../audit/README.md#core-runtime-and-http-audit-2026-10-24).

`ErrorReporting` is a trait for collecting and filtering structured messages by severity. Its name does not indicate that it registers PHP's error or exception callbacks.

## Evidence and limits

`LoggerErrorConfigurationTest.php` contains assertions for the `log_error()` helper with `log_errors` enabled and disabled. The test source was inspected, not run. No logger helper was called and no file was written during this audit.
