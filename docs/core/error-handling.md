# Error and exception handling

SimpleRest has two distinct exception paths: `FrontController` catches a `Throwable` and delegates it to a configured handler; an uncaught exception can instead reach the PHP global handler installed by the `error_handling` option. The paths have different status and disclosure behavior.

## FrontController error handler

The default `ErrorHandler` logs the exception message and its file/line, clears active output buffers, then chooses JSON for CLI or when `Request::acceptsJson()` is true. Otherwise it renders `error.php` and exits. Its HTTP status is the exception integer code when that code is between 100 and 599; every other code maps to 500.

The JSON path passes `$e->getMessage()` to `Response::error()`. The HTML view also receives that message. This handler does not redact the message based on `debug`, and its status mapping does not read `BaseException::getHttpStatus()`.

## Global uncaught-exception handler

When `error_handling` is enabled, constructors for `Controller`, `Model`, and `View` can call `set_exception_handler()` with the `ExceptionHandler` trait (the Model path also depends on its `load_config` option). This registers a global uncaught-exception callback; it does not register a PHP error handler for warnings or notices. The last such registration becomes the active global handler.

The trait attempts to roll back and close database connections. It logs through `log_error()` when `log_errors` is enabled and logs a trace only when both `debug` and `log_stack_trace` allow it. In CLI it prints diagnostic data and exits with status 1. For HTTP it chooses JSON when `remove_api_slug` is enabled, the path starts with the API segment, or the `Accept` header contains `application/json`; debug mode controls whether the real message and trace are shown. Its HTTP response status is 500.

`FrontController`'s configured `ErrorHandler` and this global handler are separate. A `Throwable` caught inside `FrontController` does not reach the global handler through that same exception. `TemporaryExceptionHandler` reuses the trait but is not registered by bootstrap in the inspected path.

## Exception families

`BaseException` adds an error code, arguments, HTTP-status metadata, and `toArray()`. Examples include `InvalidRequestBodyException` and `MiddlewareNotFoundException`. Other framework exceptions extend PHP's `Exception` or `InvalidArgumentException`; `SqlException` also stores query/data values and logs from its constructor. These class properties do not imply that each handler serializes the same metadata.

## Evidence and limits

The paths are source-traced through `FrontController`, `ErrorHandler`, `ExceptionHandler`, `Controller`, `Model`, `View`, and the exception classes. No exception was thrown to exercise either path. Logging gates and source differences are recorded in the [core runtime audit](../audit/README.md#core-runtime-and-http-audit-2026-10-24).
