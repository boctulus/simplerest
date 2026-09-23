# Database

## Verified documentation

- [Connection selection and PDO adapter paths](connections.md) — source-level behavior only; live database connectivity and driver compatibility were not reproduced.
- [Core Query Builder read path](query-builder.md) — source-level construction, SQL building, binding, and conditional soft-delete filtering; no query was run.
- [Schema descriptor generation](schemas.md) — source-level descriptor format and generator paths; no generator or DDL operation was run.

## Pending audit

Query Builder write operations, execution modes, transactions, raw-query contracts, pagination, aggregates, relation loading, automatic joins, schema DDL/type/relation behavior, migrations, tenant isolation, and per-driver query compatibility remain under audit. See the [pending audit area](../audit/pending/README.md), [database claim-level evidence](../audit/README.md#claim-level-findings-database-connections), and [schema and automatic API findings](../audit/README.md#claim-level-findings-schema-generation-and-automatic-api).
