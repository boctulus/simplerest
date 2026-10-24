# ACL implementations

The core ACL builder and evaluator are documented in [ACL and API authorization](acl.md). This page distinguishes the core contract from the two optional database-backed ACL packages found in this checkout. Core does not auto-select a package; the application config chooses the concrete implementation.

## Core contract

[`Core\Security\Acl`](../../src/framework/Security/Acl.php) is abstract. It holds role/resource/special-permission declarations and evaluates them through a snapshot and engine. An application supplies a concrete subclass through `config/acl.php` and the auth factory. The package names, Composer metadata, and provider declarations do not by themselves install or select an implementation.

## Optional package behavior

| Package | Source behavior | Important boundary |
| --- | --- | --- |
| `boctulus/simplerest-basic-acl` | Its `Boctulus\BasicACL\Acl` subclass loads the `roles` table and records role IDs/names before using the core builder. | It does not add a database-backed special-permission catalog or a user-deny loader in its subclass. |
| `boctulus/simplerest-fine-grained-acl` | Its `Boctulus\FineGrainedACL\Acl` subclass reads `sp_permissions` and `roles` through the default connection, adds fresh table/special-permission reads, persists new roles unless catalog persistence is deferred, and overrides the user resource-deny loader. | An empty `sp_permissions` table throws during setup. The override returns user-deny rows only when called with a non-null UID; not every core ACL helper supplies one. See the [authorization engine reference](authorization-engine.md) and [claim ledger](../audit/authentication-acl.md#api-extension-and-source-location-findings-2026-10-24). |

The FineGrainedACL deferred catalog path can reconcile configured roles with the `roles` table in a transaction. It checks existing role-name/ID mappings and assigned roles before creating missing entries. That path writes application database state; it was inspected but not executed.

Both package manifests label the packages proprietary and declare a SimpleRest 2.x/3.x dependency. Their service-provider metadata names provider classes, but the checked-in `register()` and `boot()` methods are empty. The current application's [`config/acl.php`](../../config/acl.php) selects FineGrainedACL explicitly; this is application configuration, not automatic framework discovery.

## Security and verification limits

FineGrainedACL's user-deny hook does not establish a global deny guarantee. `Acl::buildAuthContext()` and the default `hasSpecialPermission()` call pass a null UID, and `hasResourcePermission()` builds a context without user-deny rows. Call-site behavior must be checked before treating user-deny records as effective on every path. This source-level boundary is also in the [audit ledger](../audit/authentication-acl.md#api-extension-and-source-location-findings-2026-10-24).

The package code and focused test sources were inspected only. No Composer installation, ACL construction against the application DB, role reconciliation, permission query, or authorization request was performed.

Sources: [`BasicACL`](../../packages/boctulus/simplerest-basic-acl/src/Acl.php), [`FineGrainedACL`](../../packages/boctulus/simplerest-fine-grained-acl/src/Acl.php), their Composer manifests, [`Factory`](../../src/framework/Libs/Factory.php), and [`Core ACL`](../../src/framework/Security/Acl.php).
