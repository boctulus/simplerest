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

## Nested writes

When a controller declares connected resources through `static::$connect_to`, a request can include those resources under their relation names in a `POST`, `PUT`, or `PATCH` payload. Permission to write the parent does not grant permission to mutate each connected resource. `ApiController::authorizeNestedWrite()` is the application policy hook for those writes:

- The base implementation returns `null`, which rejects the nested mutation. Override it to authorize a specific operation and relation.
- Return a list of related-resource fields that may be written. Return `[]` to authorize relation or link changes without allowing related-resource field updates. The framework controls parent foreign keys and audit fields; related IDs and the N:M target-side pivot key are selectors, not writable related-resource fields.
- The override must enforce application rules for selected IDs, tenant or ownership scope, and link replacement or removal. The generic controller checks related-row existence and 1:N child ownership where applicable; those checks do not replace consumer-specific authorization.
- Nested array writes currently support 1:N and N:M relations. Nested 1:1 and N:1 array payloads are rejected as unsupported; direct parent foreign-key fields keep their existing behavior.

For `POST`, the parent is validated, `onPostingAfterCheck()` runs, the changed parent is validated again, and the nested policy runs before the transaction writes the parent, children, and pivots. For `PUT` and `PATCH`, parent validation and the `onPutting*AfterCheck()` hooks run before nested authorization; the parent is revalidated after the hooks. The transaction covers the controller's parent, child, and pivot writes and the post-write `onPost*`/`onPut*` hooks. Webhooks publish after commit. Pre-write hooks run before the transaction, so keep them focused on validation or data changes rather than database writes when the request includes nested resources.

## Evidence and limits

This reference describes the generic source contract. Nested-write cases are exercised through an isolated SQLite fixture, but that does not exercise an authenticated HTTP route or a consumer's ACL and domain policy. Each consumer's `authorizeNestedWrite()` override and hooks determine those rules.

Relevant sources: [`ResourceController`](../../src/framework/Api/ResourceController.php), [`ApiController`](../../src/framework/Api/ApiController.php), [`OutputHandler`](../../src/framework/Handlers/OutputHandler.php), and [`FrontController`](../../src/framework/FrontController.php).
