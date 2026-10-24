# Relationships, joins, and subresources

This page traces the framework's schema-based relationship paths. No relation query, pivot scan, or database result was run for this audit.

## Relationship metadata

Generated `ISchema` descriptors can expose `relationships`, `expanded_relationships`, and related metadata. The Query Builder's inferred joins depend on that schema being present and current; see [schema descriptors](schemas.md) for descriptor lookup and generation limits.

## Inferred joins

[`join($table, $on1, $operator, $on2, $type)`](../../src/framework/Traits/QueryBuilderTrait.php#L626) uses explicit join keys when they are supplied. When both key arguments are null, the builder enters its inference path: it requires the base model schema and relationship metadata, then reads expanded relationships to build direct joins. Multiple relationships between the same tables use generated aliases.

If no direct relationship is found, the inference path checks for a many-to-many pivot. It can join the bridge table and target table using the discovered foreign keys. [`joinTo(...$tables)`](../../src/framework/Traits/QueryBuilderTrait.php#L809) calls `join()` for each table; it still depends on the same schema and pivot prerequisites. `leftJoin()` and `rightJoin()` select their named join type, while raw, cross, and natural join methods build different paths and do not establish inferred relationship behavior.

## `connectTo()` and nested result loading

[`connectTo([...])`](../../src/framework/Traits/QueryBuilderTrait.php#L1769) enables table qualification and records the related table list. It is not a request for a flat SQL join: when [`get()`](../../src/framework/Traits/QueryBuilderTrait.php#L1877) or `first()` sees a non-empty `connect_to`, it delegates to [`SubResourceHandler::getSubResources()`](../../src/framework/Traits/SubResourceHandler.php#L24).

That handler builds correlated SQL subqueries and JSON-style aggregates. Direct relation aliases may derive from a base foreign-key name such as `subdivision_id`; pivot relation aliases are pluralized. The executed path decodes the related values and applies the base model's output mutators and transformer. With execution disabled, the handler returns the generated SQL string, but it still calls `DB::table($relatedTable)` with its default `$connect = true` while reading related field metadata. `dontExec()` therefore does not guarantee an offline `connectTo()` path. The query shape and decoded value shape depend on the relation metadata and SQL functions available to the configured database.

::: warning Relation results have an incomplete cache key
The handler's static result cache key includes the base table, related-table list, connection identifier, and `pristine` flag. It omits base query filters and the handler's `subquery_conditions`. Calls with different filters can therefore share a cache key within one process. No filtered relation result was run to reproduce this behavior.
:::

## Pivot prerequisites and nested writes

[`DBRels::getPivot()`](../../src/framework/Libs/DBRels.php#L431) reads the generated `Pivots.php` artifact. If it is absent, the code attempts to invoke `MakePivotScanCommand` when that class is available; otherwise it throws with a setup instruction. An inferred many-to-many join can reach this path. The scan command and generated-file behavior can write application schema artifacts, so they were not invoked during this audit. The inspected `DBRelsTest` source states that its full pivot path requires generated files and command infrastructure.

`Model` also composes `InsertWithSubResourcesTrait`, but its [`insertStruct()`](../../src/framework/Traits/InsertWithSubResourcesTrait.php#L33) implementation is incomplete and reaches a debug dump before any complete nested persistence workflow. `connectTo()` documents a read path; it does not establish nested insert support.

## Evidence and limits

`unit-tests/query-builder/SubResourceHandlerTest.php` contains SQL assertions for two direct relations. Those assertions were inspected, not run, and do not establish pivot behavior, a live result shape, cache correctness, or driver portability. The [database audit](../audit/README.md#database-re-audit-2026-10-24) records the cache, pivot-generation, and generated-SQL limitations.
