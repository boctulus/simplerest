# Collections API

`Collections` is a built-in API controller for saving a set of record IDs against one entity and later updating or deleting those records in bulk. `ApiHandler` reserves the `collections` resource slug and resolves it to this controller. Its behavior is distinct from the generic CRUD path described in [automatic API resolution](automatic-endpoints.md).

## Stored collection

`POST /api/{version}/collections` expects a JSON body with `entity` and `refs`. The controller resolves the entity model, rejects a set of ACL, folder, role, and user-permission tables (plus the configured users table), and writes a `collections` row containing the entity name, JSON-encoded references, and current user ID. On success it sends status 201 with the new collection ID.

## Updating the referenced records

`PUT` and `PATCH` use the collection ID in the route. The controller loads the saved collection, checks that the current user owns it unless `write_all_collections` is granted, resolves the saved entity model, and validates the submitted fields against that model's rules. Without the special permission, the target model must have a `belongs_to` field and the update is scoped to the current user.

The ordinary update runs in a database transaction: it applies the submitted fields to the stored reference IDs and deletes the collection row. The body field `refs` is ignored; the saved reference list is used. The `remove=true` branch instead deletes the collection metadata row directly. That branch does not perform the owner or `write_all_collections` check present in the update branch; this source-level boundary is recorded in the [authentication and ACL ledger](../audit/authentication-acl.md#api-extension-and-source-location-findings-2026-10-24). No request was run.

## Bulk deletion

`DELETE /api/{version}/collections/{id}?entity={entity}` requires both the collection ID and an `entity` query value matching the entity saved in the collection. It checks ownership or `write_all_collections`, then deletes the saved IDs through the model inside a transaction and removes the collection row. Without the special permission, the model must expose `belongs_to` so the operation can add the current user scope.

The constructor seeds its callable list with collection methods when `Request::authMethod()` reports a credential, then calls the `ApiController` constructor. Credential validity still depends on `auth()->check()`; the controller also performs its own ownership and special-permission checks on the branches described above. No database-backed authorization behavior was reproduced.

## Evidence and limits

The source for this reference is [`Collections`](../../src/framework/Api/Collections.php), together with [`ApiHandler`](../../src/framework/Handlers/ApiHandler.php) and the `collections` route in the API resolver. `ApiCollectionsTest.php` and `CollectionEntityValidationTest.php` were inspected, not run. No collection row or referenced record was created, changed, or deleted during this audit.
