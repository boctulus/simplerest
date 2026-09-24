# Pending framework pages

These documents are quarantined audit inputs, not current framework specifications:

- `AutomaticEndpoints-Summary.md` — resolver/model/callable paths are source-traced in the canonical notes; CRUD, query parameters, authorization, and versioning claims remain pending.
- `For-Reasoning-Architecture.md` — proposal containing unverified runtime profiles and CLI examples.
- `Framework-Architecture.md` — architecture inventory, handler lifecycle, counts, and performance claims.
- `QuickStart.md` — installation/bootstrap claims audited, but neither complete installation workflow was reproduced; API, database, auth, and CLI instructions remain pending.
- `FrontController.md` — handler pipeline claims; the source-traced conditional flow is in `docs/core/request-lifecycle.md`, while customization claims remain pending.
- `Request.md` — request parsing is covered only as part of the lifecycle; helper API claims remain pending.
- `Response.md` — `flush()` is covered only as part of the lifecycle; output and helper API claims remain pending.
- `Routing.md` — resolver and dispatch flow are summarized in `docs/core/request-lifecycle.md`; route DSL, packages, and CLI claims remain pending.
- `WebRouter.md` — compilation/matching flow is summarized in `docs/core/request-lifecycle.md`; usage examples remain pending.
- `ORM-STATUS.md` — legacy ORM and feature assertions; all claims remain under review.
- `Multi-Tenant.md` — connection selection partially audited; old connection API and broad driver list rejected, while tenancy and per-operation behavior remain pending.
- `QueryBuilder.md` — selected source paths for `select`, `where`, `toSql`, `get`, and soft-delete SQL are audited; writes, advanced methods, and runtime behavior remain pending.
- `AutoJoins.md` — relationship-inference branch found in source; schema relationship, pivot, and end-to-end behavior remain pending.
- `SubResources.md` — `connectTo()` read dispatch and test assertions inspected; nested CRUD, routes, authorization, and schema resolution remain pending.
- `PGSQL-Compatibility.md` — legacy driver/method matrix and test instructions remain unverified; no PostgreSQL tests were run.
- `PGSQL-Known-Issues.md` — historical compatibility summary and dated fix statuses have not been revalidated.
- `Query-Builder-Comparison.md` — feature comparison remains unaudited; unmeasured performance figures are rejected as current measurements.
- `Release-Status.md` — historical release snapshot; most feature, metric, and test-count claims remain unverified.
- `Schemas.md` — generated descriptor shape and CLI source path are traced; schema-alone CRUD and automatic migration claims are rejected; DDL types, relation semantics, and runtime validation remain pending.
- `SimpleRest-Complete-Docs.md` — broad feature claims and external-framework comparisons.
- `SimpleRest-Philosophy.md` — design and performance assertions not established as current contracts.

The filenames are preserved to keep the audit trail recognizable. None of these pages is linked as a canonical usage guide. See the [audit register](../../README.md) and [documentation map](../../../README.md).
