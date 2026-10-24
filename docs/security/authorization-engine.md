# Authorization engine and security utilities

The core ACL builder API is documented in [ACL and API authorization](acl.md). This page covers the lower-level snapshot, context, engine, policy, hierarchy, and permission-format classes under `Core\Security`. They provide authorization primitives; their presence does not install an HTTP guard or make every controller use them.

## Snapshot, context, and engine

[`AclSnapshot`](../../src/framework/Security/Snapshot/AclSnapshot.php) is an immutable value containing role declarations, parent-role names, valid special-permission names, compiled role allows/denies, and role-level explanation data. [`AclContext`](../../src/framework/Security/Domain/AclContext.php) is an immutable value for a decision, including user/role state and optional compiled user permissions and deny maps.

[`AclEngine`](../../src/framework/Security/Engine/AclEngine.php) evaluates an explicit `AclSnapshot` and `AclContext`. The `AuthorizationServiceInterface` lists resource and special-permission checks, membership checks, permission-set checks, role permission dominance, policy evaluation, and explicit-deny checks. `AclEngineInterface` extends that contract for compatibility; new type declarations can use `AuthorizationServiceInterface`.

The `Acl` facade exposes `getSnapshot()` and `getEngine()`. A snapshot/engine built from the ACL's in-memory declarations does not query the application database or establish that the configured ACL provider is active. Methods such as `satisfiesPolicy()`, `hasAllPermissions()`, and `hasAnyPermission()` construct a context from the current auth object.

## Policies and permission comparison

`AuthorizationPolicyInterface::isSatisfiedBy()` receives both an `AclContext` and an `AclEngineInterface`; a policy can express a capability check without embedding role names. `Acl::satisfiesPolicy()` delegates to the engine using a context built from current auth state.

`roleDominates(candidate, target)` compares effective permission sets: it asks whether the candidate's set contains every permission in the target's set. It does not use role lineage. `RoleHierarchyService` exposes lineage operations such as ancestry and `isHigherRole()`, which returns `true`, `false`, or `null` for higher, not higher, or unrelated roles. These are different comparisons; see [role inheritance in ACL](acl.md).

The current facade context builder passes `userId: null` to user-deny hooks. The FineGrainedACL hook returns no user rows for a null ID, so facade methods using that context do not establish user-deny enforcement. `hasResourcePermission()` also builds a context without user-deny rows. The exact source boundary and audit classification are in the [claim ledger](../audit/authentication-acl.md#api-extension-and-source-location-findings-2026-10-24).

## Capability and table-permission helpers

[`CapabilityTypeResolver`](../../src/framework/Security/Domain/CapabilityTypeResolver.php) labels a permission as a resource action, recognized system special permission, domain special permission, or unknown input. A domain permission is recognized from the supplied DB-name list or a dot in its name. The class can enumerate known resources from a snapshot and suggest the closest candidate by Levenshtein distance, up to a distance of three. The current CLI `acl can` and `acl explain` commands use this resolver; their command-line behavior is documented in the [CLI catalog](../cli/commands.md), and no command was run here.

[`TbPermissionBits`](../../src/framework/Security/Compiler/TbPermissionBits.php) maps table actions to packed bits:

| Action | Bit value |
| --- | ---: |
| `list_all` | 64 |
| `show_all` | 32 |
| `list` | 16 |
| `show` | 8 |
| `create` | 4 |
| `update` | 2 |
| `delete` | 1 |

`bitFor()` returns zero for an unknown action; `unpack()` returns a boolean for each known action, and `unpackGranted()` returns only enabled actions.

[`PermissionExplanation`](../../src/framework/Security/Explanation/PermissionExplanation.php) is a value object for permission source, grant/deny mode, result, and replacement state. The compiler places deterministic role-level explanations in the snapshot. This object is not, by itself, a live authorization trace for an arbitrary user request.

## Evidence and limits

The engine, compiler, and role-hierarchy test sources were inspected; they were not run for this documentation pass. No ACL helper was called with a live identity, and no policy decision or HTTP authorization path was executed.

Sources: [`Acl`](../../src/framework/Security/Acl.php), [`AclEngine`](../../src/framework/Security/Engine/AclEngine.php), [`EffectivePermissionCompiler`](../../src/framework/Security/Compiler/EffectivePermissionCompiler.php), and the domain, snapshot, contracts, and helper classes linked above.
