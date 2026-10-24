# Database driver compatibility

This page separates PDO connection branches from compatibility of Query Builder, schema, migration, and relation operations. A connection branch does not prove that every operation works on that driver. No database driver or query was exercised for this audit.

## PDO construction paths

[`DB::getConnection()`](../../src/framework/Libs/DB.php#L120) contains PDO construction branches for `mysql`, `sqlite`, `pgsql`, and `sqlsrv`. Before the switch it maps configured `mariadb` to `mysql`, `postgres` to `pgsql`, and `mssql` to `sqlsrv`. This establishes source-level connection-construction branches only; installed PDO extensions, server reachability, and per-operation SQL compatibility were not checked.

[`DB::driver()`](../../src/framework/Libs/DB.php#L520) returns the configured value, not the locally normalized DSN value. Driver-specific code elsewhere compares that value to exact strings. As a result, an alias accepted during PDO construction may bypass branches for PostgreSQL float binding, transaction savepoints, or subresource aggregation. No alias-configured connection was opened to test those paths.

## Operation-specific evidence

| Area | Source evidence | Limit |
| --- | --- | --- |
| Connections | Four PDO branches and three construction aliases | No live connection or extension check |
| Transactions | Savepoints selected for exact `mysql`, `sqlite`, and `pgsql` values | No transaction executed; nested rollback on other values falls back to full rollback |
| Schema descriptors | `SHOW COLUMNS` and `information_schema` metadata paths | An isolated SQLite probe rejected `SHOW COLUMNS`; PostgreSQL and other generators were not run |
| Schema DDL | Backtick quoting, MySQL-style DDL and metadata queries | Not a portable DDL contract; no DDL was executed |
| Subresources | Separate MySQL/MariaDB, SQLite, and PostgreSQL aggregation expressions in source | Generated SQL uses additional functions and has not been executed per driver |
| Table listing | MySQL and PostgreSQL branches in `getTableNames()` | Other PDO branches throw as unsupported in this method |

The [schema descriptor](schemas.md) and [DDL builder](schema-builder.md) guides describe their own limits. The [relationships guide](relations.md) and [transaction guide](transactions.md) cover their corresponding source paths.

## Scope of claims

The test tree contains SQLite transaction and PostgreSQL Query Builder fixtures, but source assertions are not an execution result. SQL methods such as quoting, random ordering, `LIMIT`, JSON aggregates, DDL, and metadata queries may have different driver branches or gaps. There is no all-method portability matrix, and no blanket claim that every adapter supports the same Query Builder API. Record compatibility per operation only after tracing its SQL path and, when required, exercising it against a controlled database.
