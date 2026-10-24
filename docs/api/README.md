# HTTP and REST API

## Verified documentation

- [Automatic API resource resolution](automatic-endpoints.md) — resolver mapping, model-name derivation, generator scaffold, and front-controller checks traced to source; no complete CRUD workflow was run.
- [Resource API controllers](resource-controllers.md) — construction, CORS integration, callables, output formatting, hidden fields, and extension hooks traced to source.
- [Collections API](collections.md) — saved reference sets and their update/delete branches, including the source-level ownership boundary.
- [TrashCan API](trashcan.md) — soft-deleted record reads, restore, and permanent-delete branches with model/schema prerequisites.
- [File API endpoints](file-endpoints.md) — the checked-in upload, metadata, delete, and download routes, including the response-link discrepancy and ownership limits.
- [Webhook publishing](webhooks.md) — subscription CRUD, internal and API event publishing, connection/owner matching, conditions, callback policy and HMAC verification; focused tests cover the configured connection selector, but real database-backed delivery has not been exercised.

## Authorization status

The built-in resource controller checks credentials and derives callable actions from the ACL before invoking an action. Per-user table masks now also constrain callable CRUD methods and the list/show scope flags; the focused dispatch regression passes (9 tests, 76 assertions). See the [authentication guide](../security/authentication.md), the [ACL guide](../security/acl.md), and the [claim ledger](../audit/authentication-acl.md).

## Pending audit

The controller, response-format selection, hook declarations, special resources, and file routes now have separate source references. Serialization and CRUD results have not been exercised as HTTP requests, and there is no database-backed end-to-end workflow in this audit. Authorization has source and focused regression evidence, but a full endpoint workflow has not been run. The broad claims in the old automatic-endpoint guide remain quarantined. See the [pending audit area](../audit/pending/README.md) and [claim-level evidence](../audit/README.md#claim-level-findings-schema-generation-and-automatic-api).
