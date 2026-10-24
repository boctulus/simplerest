# Raw database queries

This reference describes the static `DB` facade's raw SQL methods from source. No raw query or write was executed for this audit.

## `select()` and `query()`

[`DB::select()`](../../src/framework/Libs/DB.php#L950) prepares SQL, binds positional values, and executes it on the selected configured connection. `DB::query()` forwards the same arguments to `select()`.

When `$applyPrefix` is true (the default), `select()` passes the SQL through `Model::addPrefix()`. Bind values with positional `?` placeholders; the source binds null, integer, boolean, string, float, and resource values. Arrays and other unsupported values throw. In the `pgsql` branch, float placeholders are wrapped in `CAST(? AS DOUBLE PRECISION)`; null placeholders are replaced with the SQL token `NULL` before binding. These paths bind values, not SQL identifiers or arbitrary SQL fragments.

With `$onlyOne = true`, the method returns one `fetch()` result; otherwise it returns `fetchAll()`. The optional cursor close occurs only on the `fetchAll()` branch. `selectOne()` always calls `select()` with `$onlyOne = true`; its own `$only_one` argument does not affect that call.

## `safeSelect()` is not a sanitizer

The comments describe [`safeSelect()`](../../src/framework/Libs/DB.php#L1082) as a buffered stored-procedure helper. On MySQL/MariaDB it enables buffered queries, then delegates to `select()`. It does not sanitize SQL. The current delegation passes `$st` into `select()`'s seventh argument (`$tb_prefix`) instead of its eighth, statement-reference argument. Do not rely on an output statement handle or ordinary prefix behavior through this method until the implementation is corrected and verified; see the [database audit](../audit/README.md#database-re-audit-2026-10-24).

## Raw writes and DDL

- [`insert()`](../../src/framework/Libs/DB.php#L1101) applies `Model::addPrefix()`, prepares and binds the statement, then attempts to return a last-insert identifier. The source checks an undeclared local `$data` while choosing an explicitly supplied primary-key value, so that return path is not a reliable contract.
- [`statement()`](../../src/framework/Libs/DB.php#L1188) also applies `Model::addPrefix()`, prepares and binds values, and returns the affected row count or `false`; caught exceptions are logged and rethrown.
- [`update()`](../../src/framework/Libs/DB.php#L1253) and [`delete()`](../../src/framework/Libs/DB.php#L1258) are aliases for `statement()`. They do not enforce that the SQL begins with `UPDATE` or `DELETE`.
- [`truncate()`](../../src/framework/Libs/DB.php#L1096) issues `TRUNCATE TABLE` without a row filter. It is a destructive operation.

The `$tenant_id` parameter on these raw methods is used as a configured connection identifier. It does not add row-level tenant predicates or establish tenant authorization. The prior selected connection is restored only when one existed; a previously null selection is not restored. See [connections and tenant limits](connections.md#tenant-and-scoped-connection-limits).

These methods accept caller-authored SQL. Binding values does not make interpolated SQL, table names, column names, or other syntax safe. `safeSelect()` is not an injection defense. The implementation paths and anomalies above are source observations only; no database effect was reproduced. No `unit-tests/` source directly calls `DB::safeSelect()` or `DB::selectOne()`; transaction tests use other raw methods as setup and do not establish these wrappers' behavior.

## Related query APIs

The fluent [`QueryBuilderTrait::query()`](../../src/framework/Traits/QueryBuilderTrait.php) compiles from a different object API. It is not the same static entry point as `DB::query()`. See [Query Builder](query-builder.md) for its SQL construction, binding, results, and write-mode limits.
