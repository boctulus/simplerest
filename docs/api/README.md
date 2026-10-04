# HTTP and REST API

## Verified documentation

- [Automatic API resource resolution](automatic-endpoints.md) — resolver mapping, model-name derivation, generator scaffold, and front-controller checks traced to source; no complete CRUD workflow was run.
- [Webhook publishing](webhooks.md) — publisher boundaries, CRUD compatibility, callback endpoint policy, HMAC verification, and synchronous delivery limits; focused fixture tests pass, but database-backed callback delivery has not been exercised.

## Authorization status

The built-in resource controller checks credentials and derives callable actions from the ACL before invoking an action. Per-user table masks now also constrain callable CRUD methods and the list/show scope flags; the focused dispatch regression passes (9 tests, 76 assertions). See the [authentication guide](../security/authentication.md), the [ACL guide](../security/acl.md), and the [claim ledger](../audit/authentication-acl.md).

## Pending audit

Request-parameter mapping, serialization, validation, and complete automatic CRUD coverage remain under audit. Authorization has source and focused regression evidence, but a database-backed HTTP request and full endpoint workflow have not been run. The broad claims in the old automatic-endpoint guide remain quarantined. See the [pending audit area](../audit/pending/README.md) and [claim-level evidence](../audit/README.md#claim-level-findings-schema-generation-and-automatic-api).
