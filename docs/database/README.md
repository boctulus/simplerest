# Database

## Verified documentation

- [Connection selection and PDO adapter paths](connections.md) — source-level behavior only; live database connectivity and driver compatibility were not reproduced.
- [Query Builder](query-builder.md) — source-level construction, method families, reads, writes, execution modes, binding boundaries, and soft-delete filtering; no database query was run.
- [Model and records](model.md) — source-level `Model` composition and `ModelRecord` hydration/persistence behavior; test fixtures were inspected but not executed.
- [Schema descriptor generation](schemas.md) — source-level descriptor format and generator paths; no generator or DDL operation was run.

## Pending audit

Database execution of writes, execution-mode safety, transactions, raw-query effects, pagination, aggregates, relation loading, automatic joins, schema DDL/type/relation behavior, migrations, tenant isolation, and per-driver query compatibility remain under audit. Source presence and SQL assertions are not presented as executed database behavior. See the [pending audit area](../audit/pending/README.md), [database claim-level evidence](../audit/README.md#claim-level-findings-database-connections), and [schema and automatic API findings](../audit/README.md#claim-level-findings-schema-generation-and-automatic-api).
