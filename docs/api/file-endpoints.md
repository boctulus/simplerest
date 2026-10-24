# File API endpoints

The current application declares dedicated WebRouter routes for file metadata, upload, deletion, and download in [`config/routes/files.php`](../../config/routes/files.php). These routes construct the framework's `Core\Api\Files` and `Core\Api\Download` classes directly. The generic API resolver and the [filesystem/upload helpers](../core/file-uploads.md) are separate contracts.

## Routes declared by this application

| Method and path | Source action | Source behavior |
| --- | --- | --- |
| `GET /api/v1/files` | `Files::get()` | Returns file metadata rows. |
| `GET /api/v1/files/{id}` | `Files::get($id)` | Returns one metadata row. |
| `POST /api/v1/files` | `Files::post()` | Accepts multipart uploads through `FileUploader`, creates rows in the `files` table, and returns status 201 with stored IDs and per-file failure information. |
| `DELETE /api/v1/files/{id}` | `Files::delete($id)` | Applies the controller's file-row and filesystem deletion path. |
| `GET /download/{id}` | `Download::get($id)` | Streams the stored file as an attachment and exits. |

These paths are declared in the checked-in application route file; this source trace does not establish that a separate consumer loads the same route file.

## Upload and optional entity attachment

The upload controller writes the physical files through `FileUploader`, then stores original name, extension, stored name, owner ID, and the supplied `guest_access` value in the `files` table. A caller with the `transfer` special permission may supply `belongs_to`; otherwise a non-guest upload is assigned to the authenticated user and a guest upload gets a null owner.

An optional multipart `entity` field selects an application controller under `Controllers\Api`. That controller must implement `attachUploadedFile($uuid, $data)`. The endpoint separately checks `write_all` or the entity's `create` permission, then returns the attachment metadata along with the file result. If attachment throws, the controller attempts to remove the new metadata row and physical file before returning an error.

The response link in `Files::post()` is built as `/get/{id}`, while this route file declares `/download/{id}`. No `/get/{id}` route was found in the inspected route files; treat the returned link as a source discrepancy until corrected. The finding is recorded in the [authentication and ACL ledger](../audit/authentication-acl.md#api-extension-and-source-location-findings-2026-10-24).

## Read, delete, and download boundaries

`Files::get()` returns database metadata; it does not stream the physical file. Its owner predicates enter only when the schema check says `belongs_to` is absent. When `belongs_to` is present, those predicates are skipped. `Files::delete()` uses the same condition shape. These source branches do not establish owner enforcement; see the [ledger finding](../audit/authentication-acl.md#api-extension-and-source-location-findings-2026-10-24). No file endpoint was exercised.

The `Files` controller sets its soft-delete flag to false. The delete path attempts to unlink the physical file before calling the model deletion method; these operations are not enclosed together in a transaction in this controller. If the schema has lock or deletion-audit fields, the controller adds its corresponding values to the model operation, but the resulting DB behavior was not run.

`Download::get()` checks the request method, looks up a stored filename, sets attachment headers, calls `readfile()`, and exits. For a guest it adds `guest_access = 1` only when that column exists in the `files` schema; the static `$guest_access` property is not consulted by this method. For a signed-in caller without `read_all`, the query includes the current UID as `belongs_to`. Effective access depends on the application schema and ACL configuration.

## Evidence and limits

Sources: [`Files`](../../src/framework/Api/Files.php), [`Download`](../../src/framework/Api/Download.php), [`config/routes/files.php`](../../config/routes/files.php), and [`FileUploader`](../../src/framework/Libs/FileUploader.php). The code and test sources were inspected only; no upload, metadata query, deletion, or download was performed.
