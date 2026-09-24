# Database connections

This page records the connection behavior traced in the current source. It does not establish that a configured database is reachable, that any PDO driver is installed, or that either clean installation workflow works.

## Configuration

[`Config::setup()`](../../src/framework/Libs/Config.php#L24) merges `config/config.php` with [`config/databases.php`](../../config/databases.php#L10). The database file defines `db_connections` and `db_connection_default`; this checkout currently sets the default identifier to `main`.

Each connection entry must define `driver` and `db_name`. The implementation also reads `host`, `port`, `user`, `pass`, `pdo_options`, and `charset`. Values and credentials are supplied by the local configuration and environment; this page does not reproduce them.

## Selecting and opening a connection

[`DB::setConnection($id)`](../../src/framework/Libs/DB.php#L69) validates that `$id` exists in `db_connections` and records it as the current connection identifier. It does not create a PDO connection.

[`DB::getConnection($id = null)`](../../src/framework/Libs/DB.php#L82) returns a cached PDO handle for the selected identifier or creates one. With an explicit identifier it selects that connection. Without one, it keeps the current identifier; if none has been selected, it chooses the sole configured connection or `db_connection_default`. If there are multiple configured connections and no default, it throws. The handle cache is keyed by connection identifier.

The source constructs PDO handles for `mysql`, `sqlite`, `pgsql`, and `sqlsrv`. It maps `mariadb` to `mysql`, `postgres` to `pgsql`, and `mssql` to `sqlsrv`. Other driver values reach the unsupported-driver exception. This is source-level adapter coverage; this audit did not check installed PDO extensions, open a database connection, or verify each driver's query compatibility.

API shape only; this sequence was not run against a configured database:

```php
DB::setConnection('main');
$pdo = DB::getConnection();
```

`DB::getConnection('main')` can also select and open the named connection in one call. Both forms require the application bootstrap to have loaded the configuration and the named connection to be usable.

## Tenant and scoped-connection limits

In `DB::select()`, `insert()`, and `statement()`, the parameter named `$tenant_id` is passed to `getConnection()` as a configured connection identifier. These methods try to restore a previously selected identifier; when there was no previous identifier, the source does not restore the null state. This does not establish tenant ownership, tenant isolation, schema-per-tenant behavior, or a naming rule for tenant connections.

The legacy `DB::connection('name')->table(...)` example is not this project's API. `DB::getConnection()` returns a PDO handle, while `DB::table()` is the separate model-backed query entry point; its Query Builder contract remains under audit.

`DB::withConnection()` and `DB::withDefaultConnection()` contain `return` statements in `finally` blocks. A local PHP probe confirmed that this suppresses a callback exception. Do not rely on these helpers to propagate callback errors; no project test for this behavior was found.

## Failure-reporting finding

When `debug` is enabled, the PDO exception handler in [`DB::getConnection()`](../../src/framework/Libs/DB.php#L220) appends `var_export()` of the selected connection entry. That entry may contain credential values, so a debug-mode connection exception may expose them. This is an implementation finding recorded for separate investigation; no framework code was changed during the audit.

## Audit status

- **Verified from source:** configuration merge, connection selection rules, lazy PDO construction/cache, and the four adapter branches plus aliases.
- **Rejected:** the legacy `DB::connection()` fluent example and its Oracle, Firebird, DB2, Informix, and Sybase entries.
- **Partial or unresolved:** live connectivity, driver extensions and per-driver query compatibility, tenant isolation, prefix-based tenancy, and exception behavior for any alternative setup.

See the [claim-level connection findings](../audit/README.md#claim-level-findings-database-connections), the [quarantined legacy page](../audit/pending/framework/Multi-Tenant.md), and the [source-traced Query Builder read path](query-builder.md).
