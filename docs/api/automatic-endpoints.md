# Automatic REST API endpoints

SimpleRest provides REST resource controllers with filtering, pagination, sorting, aggregation and related-data queries. Generate the PHP artifacts for an existing database table, configure its connection and permissions, then use the corresponding resource routes.

## Resource resolution

With the checked-in [`remove_api_slug = false`](../../config/config.php) setting, [`ApiHandler::resolve()`](../../src/framework/Handlers/ApiHandler.php) expects a version-shaped segment after the `api` slug. For an ordinary resource slug it derives an application class under the configured `Controllers\api` namespace. `trashcan` and `collections` are special core-controller mappings. The resolver returns a target class, method, arguments, and version value; it does not search database schemas or create controller classes.

[`FrontController::resolve()`](../../src/framework/FrontController.php) checks that the target class exists and that the method exists (with its `__call` exception). For non-auth API requests it also requires the request method to appear in the controller's `getCallable()` list before dispatch.

## Generate a REST resource

Enable the [scaffolding package](../packages/scaffolding.md) and generate a schema descriptor, model and API controller for an existing table:

```bash
php com make any products --schema --model --api --from=main
```

You may also use `php com make schema products`, `php com make model products` and `php com make api products` separately. The [generator](../../packages/boctulus/simplerest-scaffolding/src/Commands/make/BaseMakeCommand.php) creates the controller from the [REST controller template](../../src/framework/Templates/ApiRestfulController.php), which extends the generic [`ApiController`](../../src/framework/Api/ApiController.php). Standard GET, POST, PUT, PATCH and DELETE actions are provided by the base class.

When no model is assigned explicitly, the controller derives its model name from its class. Schema descriptors supply metadata about fields and relationships; a descriptor alone does not register an API controller. The generated controller enables the soft-delete flag, which requires the corresponding schema field to take effect. Review the generated artifacts and configure authentication and ACL before making them accessible.

## HTTP methods and authorization gate

The resolver uses the request method as the target method name. The generic `ApiController` has `get`, `post`, `put`, `patch` and `delete` paths. `head()` calls `get()` with output buffering; `options()` is empty. The constructor builds its callable list from table-level or resource ACL state, and the front controller checks `getCallable()` before dispatching a non-auth API method. A method's presence in the class therefore does not mean every caller can invoke it.

The resolver validates a version-shaped path segment and returns it with the resolved controller/method/arguments. This source path does not select a version-specific controller implementation. Resource slugs are normalized from snake_case to PascalCase; `trashcan` and `collections` map to core API classes. 

## GET request parameters

For collection reads, `ApiController::get()` consumes these parameters before calling the model/query builder:

| Parameter | Behavior and constraints |
| --- | --- |
| `_schema` | Returns the selected schema class's `get()` metadata early in the method. |
| `defs` | Returns model definitions, including related-table definitions where present. |
| `fields` | Comma-separated projection; each requested field must exist in the model attributes or the method returns a 400 error. |
| `exclude` | Comma-separated hidden fields; each field is checked against model attributes. |
| `pretty` | Enables pretty response formatting except for false-like values handled in source. |
| `page`, `pageSize` | Read through configured paginator parameter names. The page size is capped at `paginator.max_limit`; a missing page defaults to 1 when pagination is requested. The separate [`Paginator` and response-metadata layers](../database/pagination.md) are documented independently. |
| `limit`, `offset` | Used when `page`/`pageSize` were not supplied. A missing/zero limit selects the configured maximum; a positive limit is capped at that maximum. |
| `orderBy` | Passed to the Query Builder as an order expression. |
| `order[field]` | Accepted as a map when each direction is `ASC` or `DESC` (case-insensitive). The nested key `orderBy[field]` is not the branch implemented here. |
| `props` | Recognizes `min`, `max`, `sum`, `avg` or `count` with a field or `*`. The optional alias path reads the function name into the alias variable, so alias behavior is not prescribed. |
| `groupBy` | Splits a comma-separated value and passes the fields to the builder. |
| `having` | Parses a narrow function/field or alias comparison against a numeric literal. Pagination with grouped/having results is not implemented. |
| `include`, `_related` | Enter the subresource path when connected resources are configured. `include` narrows that configured set; the related resources must be configured. |
| `folder` | Selects a folder-scoped branch with folder lookup and ACL checks; the resource requires valid folder configuration and ACL. |

Ordinary collection parameters become model filters after API-specific conversion. The comparison operators are `eq`, `gt`, `gteq`, `lteq`, `lt` and `neq`. The string operators `contains`, `notContains`, `startsWith`, `notStartsWith`, `endsWith` and `notEndsWith` become LIKE predicates. `between` accepts exactly two comma-separated values and emits lower/upper comparisons; `notBetween` throws an exception stating that it is not implemented. Explicit `in` and `notIn` conversion only runs when the value contains a comma; a plain comma-separated field value is also converted to `IN`. `NULL`, `null` and the internal `null!` marker have separate null-handling branches. Unknown fields/operators are rejected in the traced paths.

Related filters use `table.field`. The table must be in the controller's configured related-table list and the field must be present in that table's schema; the code then adds a join with an internal alias. This is not a general arbitrary-table join parameter.

The controller reads and removes the `q` parameter but does not apply it as a search filter. Use field-specific filters instead. Examples:

```text
GET /api/v1/products?price[gteq]=25&order[price]=ASC&pageSize=20
GET /api/v1/products?name[contains]=juice&fields=id,name
```


## Writes and record operations

`POST` reads the body, performs schema-rule validation, and passes the selected execution mode to the model. The body flag `mode_simulate` selects simulation for recognized values; this API-side flag does not make the complete workflow safe or verified. `PUT` and `PATCH` share the `modify()` path; it passes a different required-field mode to validation and consumes `_append` for related-record handling. `DELETE` requires one ID and rejects comma-separated bulk IDs with guidance to use `Collections`. It selects soft delete only when the controller enables it and the model schema contains the relevant field.

The generated API scaffold sets its soft-delete flag to the literal `true`. The actual delete effect still depends on the resource model/schema and the configured request path. Check the configured schema and permissions before executing write operations.

## Related guides

See the [request lifecycle](../core/request-lifecycle.md), [schema descriptors](../database/schemas.md), [schema builder](../database/schema-builder.md), [ACL](../security/acl.md) and [scaffolding](../packages/scaffolding.md).
