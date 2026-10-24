# TrashCan API

The built-in `TrashCan` controller exposes records already marked as soft-deleted. `ApiHandler` reserves the `trashcan` resource slug; the resolver also normalizes `trash_can` to the same class. It relies on the application API controller and model for the requested entity.

## Entity and schema prerequisites

The entity is taken from the `entity` request header, query parameter, or body field, in that order of first non-null value. The controller converts the name to its model/controller form and requires:

- an application API controller for the entity;
- that controller's `hasSoftDelete()` to return true;
- a resolvable model with its configured deleted-at column.

Missing prerequisites produce a not-found, bad-request, or not-implemented response in the source branches. This reference does not certify the response of a live application.

## Read, restore, and permanent delete

`GET /api/{version}/trashcan/{id?}` enables `showDeleted()` and filters the read to rows whose deleted-at value is not null. When ordinary owner scoping applies, the list is scoped by the model's `belongs_to` field. If the model has no such field, the controller requires `read_all_trashcan` for reads.

`POST` is rejected with status 405. `PUT` and `PATCH` reuse the parent update path; sending `trashed: false` (boolean or the string `"false"`) clears the deleted-at field and restores the row. Other values do not trigger that restore branch. When the model has no owner field, writes require `write_all_trashcan`.

`DELETE /api/{version}/trashcan/{id}` selects an already trashed row and disables the model's soft-delete behavior for that delete, so this branch requests permanent removal. The storage/driver outcome was not exercised.

## Authorization and evidence

Normal automatic-resource permissions and TrashCan's `read_all_trashcan`/`write_all_trashcan` checks are separate mechanisms. See [ACL authorization](../security/acl.md), [automatic API resolution](automatic-endpoints.md), and the [Model soft-delete reference](../database/model.md).

The source is [`TrashCan`](../../src/framework/Api/TrashCan.php) and the special-resource mapping is in [`ApiHandler`](../../src/framework/Handlers/ApiHandler.php). `ApiTrashCanTest.php` was inspected but not run. No trashed row was read, restored, or permanently deleted during this audit.
