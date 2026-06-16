---
name: special-rest-endpoints
description: Guide for SimpleRest's cross-cutting REST endpoints — TrashCan (/api/v1/trash_can), Collections (/api/v1/collections), Files (/api/v1/files) and Folders (/api/v1/folders). Soft-delete recycle bin, bulk operations, file uploads and folder-based ACL.
---

# Special REST Endpoints Skill

Beyond the per-entity auto-endpoints (see `automatic-endpoints`), SimpleRest ships four
cross-cutting REST endpoints. They are **not** plain `{entity}` CRUD; each has its own verbs,
params and ACL. Use this skill when working with the recycle bin, bulk edits/deletes, file
uploads, or folder sharing.

## Routing — how these resolve

`ApiHandler` (`src/framework/Handlers/ApiHandler.php`) maps `api/v1/{slug}`:

- `collections`, `trash_can`/`trashcan` → `Boctulus\Simplerest\Core\Api\{Collections,TrashCan}`
  (framework controllers in `src/framework/Api/`). Auto-routed, **no route entry needed**.
- everything else (incl. `folders`) → `Boctulus\Simplerest\Controllers\Api\{Name}`
  (userland in `app/Controllers/Api/`). Auto-routed too.
- `files` is the **exception: it is NOT auto-routed**. It requires explicit route entries in
  `config/routes/files.php` (multipart upload + non-REST download path). See section 3.

Slug detection for framework controllers is normalized (lowercase, `_` stripped), so
`trash_can`, `trashcan`, `TrashCan` all resolve. **Convention: use snake_case** → `trash_can`.

Login (to get the Bearer token): `POST /api/v1/auth/login` (NOT `/auth/login`, which is a view).

---

## 1. TrashCan — `/api/v1/trash_can`

Controller: `src/framework/Api/TrashCan.php`. Recycle bin for **soft-deleted** records of ANY
entity. Has its own permissions, independent from the entity's own CRUD permissions.

**Requires `entity`** (header `entity`, query `?entity=`, or body — any of the three). The entity
must have soft-delete enabled (`static $soft_delete = true` in its controller) and a `deleted_at`
column; otherwise → 501 Not implemented.

| Verb | Route | Action |
|------|-------|--------|
| GET | `/api/v1/trash_can?entity=products` | List deleted records |
| GET | `/api/v1/trash_can/157?entity=products` | Show one deleted record |
| PUT/PATCH | `/api/v1/trash_can/157` body `{"entity":"products","trashed":false}` | **Undelete** (sets `deleted_at = NULL`) |
| DELETE | `/api/v1/trash_can/157` body `{"entity":"products"}` | **Permanent** delete (hard) |
| POST | — | 405, you cannot create a trashcan resource |

Filters/sorting work like any entity: `GET /api/v1/trash_can?entity=products&order[deleted_at]=DESC`.

ACL: needs `read_all_trashcan` (read others') / `write_all_trashcan` (recover/purge others').
Without them, a user only sees/acts on records where `belongs_to = own uid`.

> Source of truth: undelete is triggered by `trashed:false` in the body
> (`TrashCan::onPuttingAfterCheck`). Permanent delete uses `setSoftDelete(false)`.

---

## 2. Collections — `/api/v1/collections`

Controller: `src/framework/Api/Collections.php`. Group record ids of an entity to run **bulk
operations**.

| Verb | Route | Action |
|------|-------|--------|
| POST | `/api/v1/collections` body `{"entity":"products","refs":[198,199,200]}` | Create collection → returns `{"id": n}` |
| GET | `/api/v1/collections?entity=products` | List/filter collections |
| DELETE | `/api/v1/collections/{id}?entity=products` | **Bulk delete**: deletes referenced records + the collection row (one transaction) |
| PATCH/PUT | `/api/v1/collections/{id}` body `{"enabled":0}` | **Bulk field-edit**: applies the body to every referenced record, then consumes (deletes) the collection. Returns `{"affected_rows": n}` |

Bulk PATCH/PUT cannot change `refs` or the collection `id` (they're stripped). PATCH leaves
required-field checks off; PUT (`put_mode`) enforces them via the entity's validation rules.

Multi-tenant: pass tenant via `?tenantid=az` or header `X-TENANT-ID`.

ACL: `write_all_collections` (operate on others' records) or ownership (`belongs_to = uid`).
**Forbidden tables** (`$forbidden_tables`): `folders`, `folder_permissions`,
`folder_other_permissions`, `roles`, `user_roles`, `sp_permissions`, `user_sp_permissions`,
`user_tb_permissions`, users table — no collections allowed on them (privilege-escalation guard).

Safety model (by design): validity/ownership/existence are checked at PATCH/DELETE time, not at
creation; PATCH/DELETE are idempotent, so duplicate or stale collections are harmless.

---

## 3. Files — `/api/v1/files`

Controller: `src/framework/Api/Files.php`. Stores in the physical `files` table;
`static $soft_delete = false` (hard delete + unlink).

> ⚠ **REQUIRES EXPLICIT ROUTES — this is the only one of the four that is NOT auto-routed.**
> The `ApiHandler` auto-resolver does **not** wire Files. The routes live in
> `config/routes/files.php` (which must be `require`d from `config/routes.php`). Without those
> entries the endpoints return 404. Reason: multipart upload needs a manual
> `Factory::response()->flush()` inside the closure, and the download uses a non-REST path
> (`get/{uuid}`) that the `api/v1/{entity}` resolver can't express. The three required routes:
>
> ```php
> WebRouter::post('api/v1/files',        function(){ /* upload  + flush() */ });
> WebRouter::delete('api/v1/files/{id}', function($id){ /* delete + flush() */ });
> WebRouter::get('get/{id}',             function($id){ /* download (readfile+exit) */ });
> ```
>
> If you add a new file-related verb/path, add its route here too — it will NOT appear by magic.

| Verb | Route | Action |
|------|-------|--------|
| POST | `/api/v1/files` (multipart/form-data, field `file`, supports many) | Upload → `{uploaded:[{filename,uuid,link}], failures:[]}` |
| DELETE | `/api/v1/files/{uuid}` | Delete file row + physical file |
| GET | `/get/{uuid}` | Download/serve the file (the `link` returned by upload) |

**Virtual entity attach** (optional, non-disruptive): pass `entity=documents|bills` in the POST.
The file is uploaded to `files` AND a metadata row is created in that entity (which must implement
`attachUploadedFile($uuid, $data)` — see trait `FileEntity` used by `Api\Documents`, `Api\Bills`).
Authorization for the attach is checked against the entity (`create` on its table). On failure the
just-uploaded file is rolled back (`cleanupUploadedFile`).

Other fields: `belongs_to` (only with special permission `transfer`, else forced to own uid),
`guest_access` (0/1, public via `/get/{uuid}` when 1). `PUT` → 501 not implemented.

---

## 4. Folders — `/api/v1/folders`

Controller: `app/Controllers/Api/Folders.php` (userland). Standard CRUD, auto-routed. Tables:
`folders`, `folder_permissions`, `folder_other_permissions`. No `deleted_at` → `$soft_delete = false`.
`folders` fields: `id, tb, name, belongs_to, created_at` (`tb`,`name`,`belongs_to`,`created_at` required).

Two layers:

1. **Folder CRUD** — `GET/POST/PUT/DELETE /api/v1/folders` to manage folders.
2. **Folder-based ACL (cross-cutting)** — pass `?folder=<id>` on ANY resource endpoint to scope it
   to a shared folder. `ApiController` checks access via
   `FoldersAclExtension::hasFolderPermission($folder, 'r'|'w')`
   (`src/framework/FoldersAclExtension.php`): per-user grants in `folder_permissions`, public/guest
   grants in `folder_other_permissions`. A resource controller must define `static $folder_field`
   to participate.

ACL special permissions: `read_all_folders` / `write_all_folders` (superadmin has both in
`config/acl.php`). To let `property_admin` manage folders, add
`->addResourcePermissions('folders', ['read_all','write'])` in `config/acl.php`.

> Permission rows can be managed via REST: `/api/v1/folder_permissions` (per-user grants) and
> `/api/v1/folder_other_permissions` (public/guest grants) are enabled — controllers
> `app/Controllers/Api/FolderPermissions.php` / `FolderOtherPermissions.php` (minimal `ApiController`
> subclasses). None of the three folder tables has `deleted_at`, so all use `$soft_delete = false`
> (DELETE is a real delete). Rows can also be managed by SQL or from a hook.

---

## Soft delete & lock (cross-cutting)

- **Soft delete**: per controller, `static $soft_delete = true|false`. When true, DELETE marks
  `deleted_at` instead of removing; recovery/purge happens only via TrashCan.
- **Lock**: if the entity has an `is_locked` (TINYINT) column, an admin can lock a record so the
  owner can't modify/delete/recover/purge it (403). Admin needs special permission `lock`.

## Quick test (Bearer token required)

```bash
TOKEN=$(curl -s -X POST {base_url}/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"<superadmin>","password":"<pass>"}' | jq -r .data.access_token)

curl -s "{base_url}/api/v1/trash_can?entity=documents" -H "Authorization: Bearer $TOKEN"
curl -s  {base_url}/api/v1/collections                  -H "Authorization: Bearer $TOKEN"
curl -s  {base_url}/api/v1/folders                      -H "Authorization: Bearer $TOKEN"
```

## See Also

- `automatic-endpoints` — standard per-entity CRUD, filtering, pagination, sorting
- `create-api-endpoint-guide` — building a new entity controller
- `acl-config` / `acl-operations` — roles, special permissions, folder permissions
- `multi-tenant-config` — tenant id (`X-TENANT-ID`) used by Collections
- `docs/ACL.md` — narrative on TrashCan, Collections, lock and security
