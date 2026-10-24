# Automatic API resource resolution

This page records the source-traced controller and dispatch path. It does not claim that a generated CRUD workflow has been run successfully.

## Resource resolution

With the checked-in [`remove_api_slug = false`](../../config/config.php) setting, [`ApiHandler::resolve()`](../../src/framework/Handlers/ApiHandler.php) expects a version-shaped segment after the `api` slug. For an ordinary resource slug it derives an application class under the configured `Controllers\api` namespace. `trashcan` and `collections` are special core-controller mappings. The resolver returns a target class, method, arguments, and version value; it does not search database schemas or create controller classes.

[`FrontController::resolve()`](../../src/framework/FrontController.php) checks that the target class exists and that the method exists (with its `__call` exception). For non-auth API requests it also requires the request method to appear in the controller's `getCallable()` list before dispatch.

## Generator and model path

The [`make api` generator](../../app/Commands/make/BaseMakeCommand.php) writes an application controller scaffold from [`ApiRestfulController.php`](../../src/framework/Templates/ApiRestfulController.php). That subclass extends the core `ApiController`, which defines generic GET, POST, PUT, PATCH, and DELETE methods. The generated scaffold sets its soft-delete flag to `true` literally; it does not derive that value from a schema.

The base [`ApiController`](../../src/framework/Api/ApiController.php) derives a model name from the controller class when no explicit model is set, then asks `get_model_instance()` for that model. [`DBRels::getModelName()`](../../src/framework/Libs/DBRels.php) resolves it against the active connection and configured model namespace. A schema generator alone creates neither this controller nor the model. Even with those classes present, authentication, ACL callability, schema loading, a working database connection, and the request path still affect dispatch and CRUD results.

The source confirms separate schema, model, API-controller, combined-generator, resolver, and dispatch paths. It does not establish that a clean `make any <table> --schema --model --api` workflow produces working CRUD. No generator or API request was run. The following sections trace the request-to-controller and GET parameter paths from source; they do not certify a live endpoint or database result.

## HTTP methods and authorization gate

The resolver uses the request method as the target method name. The generic `ApiController` has `get`, `post`, `put`, `patch` and `delete` paths. `head()` calls `get()` with output buffering; `options()` is empty. The constructor builds its callable list from table-level or resource ACL state, and the front controller checks `getCallable()` before dispatching a non-auth API method. A method's presence in the class therefore does not mean every caller can invoke it.

The resolver validates a version-shaped path segment and returns it with the resolved controller/method/arguments. This source path does not select a version-specific controller implementation. Resource slugs are normalized from snake_case to PascalCase; `trashcan` and `collections` map to core API classes. These mappings are source-traced and were not exercised over HTTP.

## GET request parameters traced in source

For collection reads, `ApiController::get()` consumes these parameters before calling the model/query builder:

| Parameter | Source behavior and limit |
| --- | --- |
| `_schema` | Returns the selected schema class's `get()` metadata early in the method. |
| `defs` | Returns model definitions, including related-table definitions where present. |
| `fields` | Comma-separated projection; each requested field must exist in the model attributes or the method returns a 400 error. |
| `exclude` | Comma-separated hidden fields; each field is checked against model attributes. |
| `pretty` | Enables pretty response formatting except for false-like values handled in source. |
| `page`, `pageSize` | Read through configured paginator parameter names. The page size is capped at `paginator.max_limit`; a missing page defaults to 1 when pagination is requested. |
| `limit`, `offset` | Used when `page`/`pageSize` were not supplied. A missing/zero limit selects the configured maximum; a positive limit is capped at that maximum. |
| `orderBy` | Passed to the Query Builder as an order expression. |
| `order[field]` | Accepted as a map when each direction is `ASC` or `DESC` (case-insensitive). The nested key `orderBy[field]` is not the branch implemented here. |
| `props` | Recognizes `min`, `max`, `sum`, `avg` or `count` with a field or `*`. The optional alias path reads the function name into the alias variable, so alias behavior is not prescribed. |
| `groupBy` | Splits a comma-separated value and passes the fields to the builder. |
| `having` | Parses a narrow function/field or alias comparison against a numeric literal. The source notes that grouped/having pagination is still missing. |
| `include`, `_related` | Enter the subresource path when connected resources are configured. `include` narrows that configured set; the relationship result was not reproduced. |
| `folder` | Selects a folder-scoped branch with folder lookup and ACL checks; this guide does not claim a successful folder request. |

Ordinary collection parameters become model filters after API-specific conversion. The traced comparison operators are `eq`, `gt`, `gteq`, `lteq`, `lt` and `neq`. The string operators `contains`, `notContains`, `startsWith`, `notStartsWith`, `endsWith` and `notEndsWith` become LIKE predicates. `between` accepts exactly two comma-separated values and emits lower/upper comparisons; `notBetween` throws an exception stating that it is not implemented. Explicit `in` and `notIn` conversion only runs when the value contains a comma; a plain comma-separated field value is also converted to `IN`. `NULL`, `null` and the internal `null!` marker have separate null-handling branches. Unknown fields/operators are rejected in the traced paths.

Related filters use `table.field`. The table must be in the controller's configured related-table list and the field must be present in that table's schema; the code then adds a join with an internal alias. This is not a general arbitrary-table join parameter.

The controller reads and removes the `q` parameter, but the traced `get()` path does not use its value to build a search predicate. Do not rely on `q` search in this version. Examples of parameter syntax below are source-shaped illustrations only:

```text
GET /api/v1/products?price[gteq]=25&order[price]=ASC&pageSize=20
GET /api/v1/products?name[contains]=juice&fields=id,name
```

Neither request was executed. Query Builder support alone is not evidence that a parameter is implemented by this API path.

## Writes and record operations

`POST` reads the body, performs schema-rule validation, and passes the selected execution mode to the model. The body flag `mode_simulate` selects simulation for recognized values; this API-side flag does not make the complete workflow safe or verified. `PUT` and `PATCH` share the `modify()` path; it passes a different required-field mode to validation and consumes `_append` for related-record handling. `DELETE` requires one ID and rejects comma-separated bulk IDs with guidance to use `Collections`. It selects soft delete only when the controller enables it and the model schema contains the relevant field.

The generated API scaffold sets its soft-delete flag to the literal `true`. The actual delete effect still depends on the resource model/schema and the configured request path. No create, update, delete, simulation, relation write, or authorization request was run here.

## Evidence boundary

`ApiTest.php`, `ProductsBasicTest.php` and `ProductsPaginationTest.php` contain API test cases against the checked-in application setup. They were inspected but not run. They do not call the schema/model/API generators as a clean end-to-end workflow. Each filter, parameter, ACL branch and relation still requires runtime evidence before it can be described as a reproduced endpoint contract.

See the [claim-level audit](../audit/README.md#claim-level-findings-schema-generation-and-automatic-api), the [request lifecycle](../core/request-lifecycle.md), [schema descriptors](../database/schemas.md), the [Schema DDL builder](../database/schema-builder.md), and the [pending legacy endpoint guide](../audit/pending/framework/AutomaticEndpoints-Summary.md).
