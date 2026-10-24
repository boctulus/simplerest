# Database

## Verified documentation

- [Connection selection and PDO adapter paths](connections.md) — source-level behavior only; live database connectivity and driver compatibility were not reproduced.
- [Query Builder](query-builder.md) — source-level construction, method families, reads, writes, execution modes, binding boundaries, and soft-delete filtering; no database query was run.
- [Model and records](model.md) — source-level `Model` composition and `ModelRecord` hydration/persistence behavior; test fixtures were inspected but not executed.
- [Schema descriptor generation](schemas.md) — source-level descriptor format and generator paths; no generator or DDL operation was run.
- [Schema DDL builder](schema-builder.md) — source-level column, index, foreign-key, create/alter, and metadata API; no table DDL was executed.
- [Migration lifecycle](migrations.md) — source-traced file loading, tracking, command effects, and simulation limits; no migration or database operation was run.
- [Relationships, joins, and subresources](relations.md) — schema-inferred joins, pivot prerequisites, nested result loading, cache limits, and incomplete nested writes; no related query or pivot scan was run.
- [Database transactions](transactions.md) — source-traced connection scoping, savepoints, and callback behavior; no transaction was run.
- [Raw database queries](raw-queries.md) — static `DB` SQL, binding, prefix, write, and destructive-operation paths; no raw statement was run.
- [Database driver compatibility](driver-compatibility.md) — PDO construction branches separated from per-operation SQL/DDL support; no driver matrix was executed.

## Pending audit

Database execution of writes, execution-mode safety, transactions, raw-query effects, pagination, aggregates, relation loading, automatic joins, DDL behavior by driver, migration execution, tenant isolation, and per-driver query compatibility remain unverified. Those source paths now have distinct references; source presence and SQL assertions are not presented as executed database behavior. See the [pending audit area](../audit/pending/README.md), [database claim-level evidence](../audit/README.md#claim-level-findings-database-connections), and [schema and automatic API findings](../audit/README.md#claim-level-findings-schema-generation-and-automatic-api).
