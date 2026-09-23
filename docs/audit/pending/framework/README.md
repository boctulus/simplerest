# Pending framework pages

These documents are quarantined audit inputs, not current framework specifications:

- `AutomaticEndpoints-Summary.md` — endpoint coverage, query parameters, authorization, and versioning claims.
- `For-Reasoning-Architecture.md` — proposal containing unverified runtime profiles and CLI examples.
- `Framework-Architecture.md` — architecture inventory, handler lifecycle, counts, and performance claims.
- `QuickStart.md` — installation/bootstrap claims audited, but neither complete installation workflow was reproduced; API, database, auth, and CLI instructions remain pending.
- `FrontController.md` — handler pipeline claims; the source-traced conditional flow is in `docs/core/request-lifecycle.md`, while customization claims remain pending.
- `Request.md` — request parsing is covered only as part of the lifecycle; helper API claims remain pending.
- `Response.md` — `flush()` is covered only as part of the lifecycle; output and helper API claims remain pending.
- `Routing.md` — resolver and dispatch flow are summarized in `docs/core/request-lifecycle.md`; route DSL, packages, and CLI claims remain pending.
- `WebRouter.md` — compilation/matching flow is summarized in `docs/core/request-lifecycle.md`; usage examples remain pending.
- `ORM-STATUS.md` — legacy ORM and feature assertions; all claims remain under review.
- `Release-Status.md` — historical release snapshot; most feature, metric, and test-count claims remain unverified.
- `Schemas.md` — legacy schema guide moved here during API audit; its schema-alone automatic CRUD claim is rejected, and its other claims remain unreviewed.
- `SimpleRest-Complete-Docs.md` — broad feature claims and external-framework comparisons.
- `SimpleRest-Philosophy.md` — design and performance assertions not established as current contracts.

The filenames are preserved to keep the audit trail recognizable. None of these pages is linked as a canonical usage guide. See the [audit register](../../README.md) and [documentation map](../../../README.md).
