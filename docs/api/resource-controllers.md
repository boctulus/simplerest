# Resource API controllers

This reference describes the shared HTTP behavior and extension points of the built-in resource controllers. Resource discovery, GET parameter mapping, CRUD generation, and ACL callability are covered in [automatic API resolution](automatic-endpoints.md). The request-wide HTTP flow is in [request lifecycle](../core/request-lifecycle.md), and CORS matching is in [the core CORS reference](../core/cors.md).

## Controller construction

`ResourceController` is the common base for framework API resources. Its constructor returns immediately under CLI. For HTTP it invokes `cors()`, emits any headers declared by the subclass, calls `response()->sendOK()` for an `OPTIONS` request, then calls `auth()->check()` and constructs the ordinary `Controller` base. `Response::sendOK()` only sets status 200 and returns the response singleton; this call does not itself flush or exit. An allowed CORS preflight can exit earlier inside `CorsHandler`; see [the core CORS reference](../core/cors.md).

`ApiController` extends `ResourceController`. It derives a model and table from the controller name unless the subclass supplies them, selects a tenant connection when a tenant ID is present, and builds callable HTTP methods from the effective request method and ACL state. `FrontController` checks that list before dispatching a non-auth API action. Construction and its side effects have already happened at that point. See [authentication](../security/authentication.md) and [ACL authorization](../security/acl.md) for credential and permission details.

The built-in `ApiController` constructor also calls impersonation classes that are not present in this repository. The unresolved dependency is recorded in the [controller dependency issue](../issues/api-controller-missing-impersonation-types.md); this source reference does not claim that a standalone consumer can construct every resource successfully.

## Action and output behavior

The generic controller defines `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `HEAD`, and `OPTIONS` methods. `HEAD` buffers a `GET` call and discards its body; the `OPTIONS` action is empty. HTTP `OPTIONS` passes through the constructor unless `CorsHandler` exits for an allowed preflight.

`ApiController` does not provide a separate serializer interface. Its actions commonly pass arrays or values to `Response`; the `OutputHandler` chooses JSON automatically for an `ApiController` only when an action returns non-null data to the `FrontController`. A direct `response()->send()` call follows the response object's own path. See [Request and Response](../core/request-response.md) for response emission.

Subclasses can declare `static::$hidden` and enable `static::$hide_in_response` to remove named attributes from ordinary single-record and list responses. The source notes that this filtering does not currently apply to subresource data. It is response shaping, not a field-level ACL or a storage rule.

## Extension hooks

The protected hooks let an application subclass add work around the generic resource branches. The hook declarations are in [`ApiController`](../../src/framework/Api/ApiController.php); folder-specific hooks run only in folder branches.

| Operation | Hook families |
| --- | --- |
| Read | `onGettingBeforeCheck`, `onGettingAfterCheck`, `onGettingAfterCheck2`, `onCounting`, `onGot` |
| Create | `onPostingBeforeCheck`, `onPostingAfterCheck`, `onPost` |
| Update | `onPuttingBeforeCheck`, `onPuttingBeforeCheck2`, `onPuttingAfterCheck`, `onPut` |
| Delete | `onDeletingBeforeCheck`, `onDeletingAfterCheck`, `onDeleted` |
| Folder access | `onGettingFolder*`, `onDeletingFolder*`, `onPostingFolder*`, and `onPuttingFolder*` |

The write hooks receive data by reference where their signatures declare `&$data`; a subclass can therefore change fields before or after validation. Exact order differs by branch, so inspect the method being extended instead of assuming every hook runs for every request. CRUD event publication is separate and documented in [webhooks](webhooks.md).

## Evidence and limits

This is a source trace. The API controller, output formatter, and their tests were inspected, but no API request, response serialization, or database-backed CRUD operation was executed. It does not establish that every generated controller is loadable or that a subclass hook is safe for a particular write.

Relevant sources: [`ResourceController`](../../src/framework/Api/ResourceController.php), [`ApiController`](../../src/framework/Api/ApiController.php), [`OutputHandler`](../../src/framework/Handlers/OutputHandler.php), and [`FrontController`](../../src/framework/FrontController.php).
