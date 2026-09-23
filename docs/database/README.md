# Database

## Verified documentation

- [Connection selection and PDO adapter paths](connections.md) — source-level behavior only; live database connectivity and driver compatibility were not reproduced.
- [Core Query Builder read path](query-builder.md) — source-level construction, SQL building, binding, and conditional soft-delete filtering; no query was run.

## Pending audit

Query Builder write operations, execution modes, transactions, raw-query contracts, pagination, aggregates, relation loading, automatic joins, schemas, migrations, tenant isolation, and per-driver query compatibility remain under audit. See the [pending audit area](../audit/pending/README.md) and [claim-level evidence](../audit/README.md#claim-level-findings-query-builder).
