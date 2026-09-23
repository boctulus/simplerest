# Automatic API resource resolution

This page records the source-traced controller and dispatch path. It does not claim that a generated CRUD workflow has been run successfully.

## Resource resolution

With the checked-in [`remove_api_slug = false`](../../config/config.php) setting, [`ApiHandler::resolve()`](../../src/framework/Handlers/ApiHandler.php) expects a version-shaped segment after the `api` slug. For an ordinary resource slug it derives an application class under the configured `Controllers\api` namespace. `trashcan` and `collections` are special core-controller mappings. The resolver returns a target class, method, arguments, and version value; it does not search database schemas or create controller classes.

[`FrontController::resolve()`](../../src/framework/FrontController.php) checks that the target class exists and that the method exists (with its `__call` exception). For non-auth API requests it also requires the request method to appear in the controller's `getCallable()` list before dispatch.

## Generator and model path

The [`make api` generator](../../app/Commands/make/BaseMakeCommand.php) writes an application controller scaffold from [`ApiRestfulController.php`](../../src/framework/Templates/ApiRestfulController.php). That subclass extends the core `ApiController`, which defines generic GET, POST, PUT, PATCH, and DELETE methods. The generated scaffold sets its soft-delete flag to `true` literally; it does not derive that value from a schema.

The base [`ApiController`](../../src/framework/Api/ApiController.php) derives a model name from the controller class when no explicit model is set, then asks `get_model_instance()` for that model. [`DBRels::getModelName()`](../../src/framework/Libs/DBRels.php) resolves it against the active connection and configured model namespace. A schema generator alone creates neither this controller nor the model. Even with those classes present, authentication, ACL callability, schema loading, a working database connection, and the request path still affect dispatch and CRUD results.

The source confirms separate schema, model, API-controller, combined-generator, resolver, and dispatch paths. It does not establish that a clean `make any <table> --schema --model --api` workflow produces working CRUD. No generator was run; the database/PHPUnit tests inspected use an already-configured application and do not call the generators. Query filters, projections, ordering, API pagination parameters, aggregates, relationship inclusion, ACL details, and version-specific controller dispatch remain outside this page.

See the [claim-level audit](../audit/README.md#claim-level-findings-schema-generation-and-automatic-api), the [request lifecycle](../core/request-lifecycle.md), [schema descriptors](../database/schemas.md), and the [pending legacy endpoint guide](../audit/pending/framework/AutomaticEndpoints-Summary.md).
