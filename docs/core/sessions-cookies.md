# Sessions and cookies

`Session` and `Cookie` are thin static wrappers around PHP's session and cookie functions. They do not define a framework-wide session policy or cookie security defaults.

## Session methods

Most data methods call `Session::start()`, which runs `session_start()` only when PHP reports `PHP_SESSION_NONE`. `set($key, $value)`, `get($key, $default)`, `has($key)`, `delete($key)`, and `clear()` read or update `$_SESSION`; `clear()` calls `session_unset()`.

`destroy()` starts a session if needed and then calls `session_destroy()`. `regenerateId($deleteOld)` delegates to `session_regenerate_id()`. `getId()` starts the session and returns `session_id()`. `getName()` and `setName()` call `session_name()` directly and do not start the session.

`flash($key, $value)` stores a value under `$_SESSION['_flash']`. Calling it with no value or with `null` reads and removes the value; therefore `null` is not a distinct storable flash value. `hasFlash($key)` checks whether that entry exists.

## Cookie methods

`Cookie::set($name, $value, $expiry = 0, $path = '/', $domain = '', $secure = false, $httponly = false)` clears the current request's `$_COOKIE[$name]` entry when present, then delegates to `setcookie()` with those positional arguments. It does not populate `$_COOKIE` with the new value; a browser sends the cookie on a later request.

`get($name = null)` reads the incoming `$_COOKIE` map or one value. `delete($name, $path = '/', $domain = '')` removes the local entry and sends an expired cookie. The wrapper does not expose SameSite on these signatures, and `delete()` does not pass secure or HttpOnly options.

## Evidence and limits

The references above are traced to `Session.php` and `Cookie.php`. No session or cookie operation was run, and no security or browser behavior is inferred from the wrappers. The source-level limits are listed in the [core runtime audit](../audit/README.md#core-runtime-and-http-audit-2026-10-24).
