# Database transactions

This page traces the transaction API in the current source. No database transaction or PHPUnit case was run for this audit.

## Connection scope

[`DB::beginTransaction()`](../../src/framework/Libs/DB.php#L805) tracks nesting by the selected connection identifier. The outer call enables PDO exception mode and calls `PDO::beginTransaction()`. The [connection-affinity guard](../../src/framework/Libs/DB.php#L102) prevents selecting another connection while an active transaction is bound; connection closure is guarded as well. This is not a distributed transaction across multiple configured connections.

Select the intended configured connection before beginning work. `DB::transaction()` takes a closure without arguments and returns its result after committing:

```php
DB::transaction(function () {
    // Database work on the currently selected connection.
});
```

This is the API shape from source, not a reproduced database workflow.

## Nested calls and savepoints

The [savepoint capability check](../../src/framework/Libs/DB.php#L731) compares exact `DB::driver()` values `mysql`, `sqlite`, and `pgsql`. A nested begin creates a `LEVEL_n` savepoint. A nested [`commit()`](../../src/framework/Libs/DB.php#L830) attempts to release that savepoint and lowers the level; a release exception is ignored. For other driver values, nested commit only lowers the level.

A nested [`rollback()`](../../src/framework/Libs/DB.php#L870) on a savepoint-capable driver rolls back to and attempts to release the savepoint. If rollback to the savepoint fails, it falls back to a full PDO rollback and clears the tracked level. When the selected driver value does not enable savepoints, a nested rollback also rolls back the entire PDO transaction. A caller therefore cannot assume inner rollback isolation on every configured driver value.

The `mariadb`, `postgres`, and `mssql` config aliases are normalized for PDO construction, while `DB::driver()` returns the configured string. The savepoint check compares the unnormalized string to the three names above. See [driver compatibility](driver-compatibility.md).

## `DB::transaction()` failure path

[`DB::transaction()`](../../src/framework/Libs/DB.php#L926) begins before entering its `try` block. If the callback returns normally, it commits and returns the callback result. If the callback or commit throws a `Throwable`, it attempts rollback, swallows a rollback failure, and rethrows the caught throwable. A failure while beginning occurs before that catch path.

The lower-level `beginTransaction()`, `commit()`, and `rollback()` methods are also available when callers need to manage a boundary explicitly. Their nesting counter is source-managed; direct PDO calls can create state the counter did not start.

## Evidence and limits

`unit-tests/query-builder/DB_TransactionTest.php` contains SQLite in-memory cases for commit, rollback, nested behavior, connection affinity, and savepoint failures. `unit-tests/query-builder/PGSQLQueryBuilderTest.php` contains PostgreSQL transaction cases. These files were inspected but not executed; they describe intended assertions and their prerequisites, not a runtime result. No transaction example on this page was run.
