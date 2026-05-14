# ACL Improvement v4 — Compiled Effective Permissions + Business Deny Rules

> Evolution from v3 (Compiled Effective Permissions) **plus** business-level DENY rules at builder and DB layers. Backward-compatible with all existing APIs, snapshots, packages and tests.

---

## Context

SimpleRest ACL evaluates permissions at runtime by looping over roles and walking arrays inside `AclEngine`. Inheritance is already flattened at build-time via `addInherit()` copy semantics, but every `can()` call still iterates `$context->roles`, looks up `$snapshot->rolePerms[$role][...]`, and runs `in_array()` scans. This is fine for small role sets, but the v3 design document calls for:

- precompiled **effective permissions** (hash maps, not lists)
- **O(1) runtime evaluation**
- a deterministic, explainable **frontend admin** model
- preserved **replacement semantics** for `user_tb_permissions`
- full backward compatibility with `Acl`, `BasicACL`, `FineGrainedACL`, existing snapshots, and the 23+ existing tests

This iteration (v4) delivers Phase A: compiler infrastructure + snapshot/context fields + engine fast path + explanations + tests, **plus** business-level deny rules. Hard security constraints (v3 §6) and role-hierarchy doc clarification (v3 §7) are deferred.

### User decisions

- **Scope**: Phases 1–5, 8, 9, 10 of v3 doc **plus business-level deny rules**.
- **Per-user compilation**: lazy by default (static factory on `AclContext`), eager opt-in surface exposed for future auth-token integration.
- **Snapshot shape**: per-role hash maps `effectiveAllows[role][resource][action] = true` + per-role `denyRolePerms` + internal `effectiveDenies` cache.
- **Deny semantics**: business-level **DENY > ALLOW** (v2-style). Declared via builder AND via DB table `user_deny_permissions`.
- **Note on deviation**: v3 §"NO DENY > ALLOW global semantics" is explicitly relaxed. The compiler's internal `effectiveDenies` cache (from replacement semantics) and the new explicit deny rules merge into a single deny map at runtime. Document in CHANGELOG and `docs/ACL.md`.

---

## Current state (verified)

| File | Role |
|---|---|
| `src/framework/Acl.php` | Builder. `addInherit()` copies parent sp/tb perms into child. `getSnapshot()` → `AclSnapshot`. |
| `src/framework/Security/Snapshot/AclSnapshot.php` | Readonly DTO: `rolePerms`, `parentRoleNames`, `validSpPerms`, `guestName`, `registeredName`. |
| `src/framework/Security/Domain/AclContext.php` | Readonly DTO: `userId`, `roles`, `authenticated`, `userSpPerms`, `userTbPerms`, `rowId`, `folderId`. `userTbPerms` is **packed bitmask** keyed by resource. |
| `src/framework/Security/Engine/AclEngine.php` | Pure orchestrator. `hasPermissionInternal()` checks `read_all`/`write_all` → user bitmask override → role lookup. Loops over `$context->roles`. |
| `src/framework/Security/Contracts/{AclEngineInterface,AuthorizationServiceInterface,AuthorizationPolicyInterface}.php` | Stable contracts. |
| `packages/boctulus/{basic-acl,fine-grained-acl}/src/Acl.php` | DB-backed builders extending core `Acl`. `FineGrainedACL` adds `getFreshTbPermissions()` / `getFreshSpPermissions()`. |
| `unit-tests/acl/AclEngineTest.php`, `unit-tests/acl/RoleHierarchyServiceTest.php` | Existing coverage — must keep green. |
| `app/Commands/MakeCommand.php` (`make acl` ≈ L529–571) | Serializes `Acl` to `storage/security/acl.cache`. Must keep working. |

Action shorthand expansion (`'read' → ['show','list']`, `'write' → ['create','update','delete']`) is performed today inside `addResourcePermissions()` in `Acl.php`. Keep that intact.

---

## Recommended approach

### 1. New compiler

Path: `src/framework/Security/Compiler/EffectivePermissionCompiler.php`
Namespace: `Boctulus\Simplerest\Core\Security\Compiler`

Pure class. No DB, no `auth()`. Two public entry points:

```php
final class EffectivePermissionCompiler
{
    /**
     * Per-role compilation. Reads $rolePerms (already flattened via addInherit)
     * and produces:
     *   [
     *     'allows'       => ['role' => ['resource' => ['action' => true]]],
     *     'denies'       => ['role' => ['resource' => ['action' => true]]], // internal negative cache
     *     'explanations' => ['role.resource.action' => [granted=>?, source=>?, mode=>?]],
     *   ]
     */
    public function compileRoles(array $rolePerms, array $denyRolePerms, array $validSpPerms): array;

    /**
     * Per-user compilation. Reuses role compilation and applies replacement
     * semantics for user_tb_permissions + additive user_sp_permissions + explicit denies.
     * Output:
     *   [
     *     'allow' => ['resource' => ['action' => true]],
     *     'deny'  => ['resource' => ['action' => true]],
     *   ]
     */
    public function compileForUser(
        AclSnapshot $snapshot,
        array       $roles,
        array       $userSpPerms   = [],
        array       $userTbPerms   = [],   // packed bitmask map
        array       $userDenyPerms = []    // explicit user deny
    ): array;
}
```

Internals:
- Extract bitmask constants into a tiny `TbPermissionBits` helper under `Compiler/` (`unpack(int): array`) so engine and compiler share one source of truth.
- `read_all` / `write_all`: when compiling per-user, expand to a sentinel `'*'` entry under the relevant action group — engine fast path checks the sentinel before the resource map.
- `user_tb_permissions` semantics: when `userTbPerms[$resource]` is set, **replace** role-derived set for that resource (absent actions → `effectiveDenies` cache).
- Explicit deny merges into `deny[resource][action]` map and beats everything else (including `read_all`/`write_all`).

### 2. AclSnapshot — new fields (defaults, no breaking change)

`src/framework/Security/Snapshot/AclSnapshot.php`

```php
public readonly array $effectiveAllows         = [], // per-role
public readonly array $effectiveDenies         = [], // per-role, internal cache (from replacement semantics)
public readonly array $denyRolePerms           = [], // per-role explicit denies (from addDenyResourcePermissions / addDenySpecialPermissions)
public readonly array $permissionExplanations  = [],
```

Old cached `acl.cache` files lacking these props rehydrate with defaults. `make acl --force` regenerates with new fields populated.

### 3. AclContext — new fields (defaults, no breaking change)

`src/framework/Security/Domain/AclContext.php`

```php
public readonly ?array $compiledPermissions = null,
public readonly array  $userDenyPerms       = [],   // ['resource' => ['action1' => true, ...]]
public readonly array  $userDenySpPerms     = [],   // ['sp_name' => true]
```

Lazy factory (eager opt-in surface):

```php
public static function withCompiled(
    AclSnapshot $snapshot,
    EffectivePermissionCompiler $compiler,
    ?int $userId, array $roles, bool $authenticated,
    array $userSpPerms = [], array $userTbPerms = [], array $userDenyPerms = [],
    ?int $rowId = null, ?int $folderId = null
): self;
```

External callers (auth-token issuance) can call `compileForUser` themselves and pass `compiledPermissions` directly into the constructor — eager path.

### 4. AclEngine — fast path with deny precedence

`src/framework/Security/Engine/AclEngine.php` — add fast lane in `hasPermissionInternal()`; leave signatures untouched.

```php
private function hasPermissionInternal(string $perm, string $resource, AclContext $context): bool
{
    if ($context->compiledPermissions !== null) {
        $cp = $context->compiledPermissions;
        // DENY > ALLOW
        if (isset($cp['deny'][$resource][$perm]))  return false;
        if (isset($cp['deny']['*'][$perm]))        return false; // wildcard deny (rare)
        // ALLOW
        if (isset($cp['allow']['*'][$perm]))       return true;  // read_all/write_all sentinel
        return isset($cp['allow'][$resource][$perm]);
    }

    // ── legacy path — also gains explicit deny check at the top ──
    if ($this->hasExplicitDeny($context, $perm, $resource)) return false;

    // ...existing read_all/write_all special perm check…
    // ...user_tb bitmask override…
    // ...role-based fallback…
}

private function hasExplicitDeny(AclContext $context, string $action, string $resource): bool
{
    // 1) user-level explicit deny (replacement-immune)
    if (isset($context->userDenyPerms[$resource][$action])) return true;
    // 2) role-level explicit deny
    foreach ($context->roles as $role) {
        if (isset($this->snapshot->denyRolePerms[$role][$resource][$action])) return true;
    }
    return false;
}
```

Add same `hasExplicitDeny` short-circuit at the top of `hasSpecialPermissionInternal()` (checking `userDenySpPerms` and role-level sp denies — keep data shape decision in the compiler).

Expose deny on public contract:

```php
// AuthorizationServiceInterface (append at end):
public function hasExplicitDeny(AclContext $context, string $action, string $resource): bool;
```

**Snapshot effectiveAllows/denyRolePerms are consulted at runtime only via the legacy path** (when `compiledPermissions` is null). When compiled, all data folds into per-user map.

### 5. PermissionExplanation

Path: `src/framework/Security/Explanation/PermissionExplanation.php`
Namespace: `Boctulus\Simplerest\Core\Security\Explanation`

```php
final class PermissionExplanation
{
    public function __construct(
        public readonly string  $resource,
        public readonly string  $action,
        public readonly bool    $granted,
        public readonly string  $source,   // 'role:admin' | 'user_tb_permissions' | 'special:read_all' | 'deny:user' | 'deny:role'
        public readonly string  $mode,     // 'inherited' | 'direct' | 'replacement' | 'wildcard' | 'deny'
        public readonly bool    $replacedRolePermissions = false,
    ) {}
}
```

Compiler populates `permissionExplanations` so admin frontend renders deterministic checkboxes.

### 6. Builder — deny APIs

Add to `src/framework/Acl.php`:

```php
public function addDenyResourcePermissions(string $table, array $actions, $to_role = null): self;
public function addDenySpecialPermissions(array $sp_permissions, $to_role = null): self;
```

Mirror `addResourcePermissions` / `addSpecialPermissions` shape (same role-targeting rules, same action shorthand expansion). Store into new property `$deny_role_perms` (serializable). Passed through to snapshot as `denyRolePerms`.

### 7. DB table + endpoint (FineGrainedACL)

Migration:

```sql
CREATE TABLE user_deny_permissions (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    user_id     INT NOT NULL,
    resource    VARCHAR(100) NOT NULL,
    action      VARCHAR(50) NOT NULL,
    created_by  INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by  INT NULL,
    updated_at  TIMESTAMP NULL,
    UNIQUE KEY uq_user_res_action (user_id, resource, action),
    INDEX idx_user (user_id)
);
```

Endpoints (REST, mirrors `user_tb_permissions`):

```
GET    /api/v1/user_deny_permissions
POST   /api/v1/user_deny_permissions   { user_id, resource, action }
DELETE /api/v1/user_deny_permissions/{id}
```

Generate via project's standard resource-controller pattern (see `create-api-endpoint-guide` skill). Migration under `app/Database/Migrations/` (verify path during impl).

Load logic in `packages/boctulus/fine-grained-acl/src/Acl.php`:

- `fetchDenyPermissions(int $uid): array` — query `user_deny_permissions` → `['resource' => ['action' => true]]`.
- `getFreshDenyPermissions(int $uid): array` — bypass cache (parallel to `getFreshTbPermissions`).
- Inject into `AclContext->userDenyPerms` when constructing context in `hasPermission()` / `can()` paths (mirror existing `userTbPerms` plumbing in `Acl.php` ≈ L352–383).

`BasicACL` does not need DB-backed denies (matches its philosophy — no fresh fetchers); builder-declared denies still work via snapshot.

### 8. Acl builder — wire compiler into `getSnapshot()`

`src/framework/Acl.php` — `getSnapshot()` (currently ≈ L619–628):

```php
public function getSnapshot(): AclSnapshot
{
    $compiler = new EffectivePermissionCompiler();
    $compiled = $compiler->compileRoles(
        $this->role_perms,
        $this->deny_role_perms ?? [],
        $this->sp_permissions
    );

    return new AclSnapshot(
        rolePerms:              $this->role_perms,
        parentRoleNames:        $this->parent_role_names,
        validSpPerms:           $this->sp_permissions,
        guestName:              $this->guest_name,
        registeredName:         $this->registered_name,
        effectiveAllows:        $compiled['allows'],
        effectiveDenies:        $compiled['denies'],
        denyRolePerms:          $this->deny_role_perms ?? [],
        permissionExplanations: $compiled['explanations'],
    );
}
```

`hasPermission()`, `hasSpecialPermission()`, `hasResourcePermission()` in `Acl` (≈ L291–383) build `AclContext` today — extend them to also pass `compiledPermissions = $compiler->compileForUser(...)`. Keep existing fallback so legacy callers building their own context without compiled perms still work.

### 9. Tests

**`unit-tests/acl/AclCompiledPermissionsTest.php`**

| # | Test | Verifies |
|---|---|---|
| 1 | `compileRoles flattens inherited perms` | Compiler expands inheritance correctly. |
| 2 | `compileForUser applies user_tb replacement` | Resource override replaces role set; absent actions go to deny cache. |
| 3 | `compileForUser additive user_sp` | User special perms add to role-derived. |
| 4 | `engine fast path uses compiledPermissions` | `can()` returns hash result without touching `$snapshot->rolePerms`. |
| 5 | `read_all sentinel grants any read` | Wildcard `'*'` works for read-class actions. |
| 6 | `legacy path still works when compiledPermissions=null` | Existing 23+ tests stay green. |
| 7 | `explanations are deterministic` | Same input → same explanations payload. |
| 8 | `snapshot deserialization with missing fields` | Old serialized snapshot rehydrates with defaults. |

**`unit-tests/acl/AclEngineDenyTest.php`**

| # | Test | Verifies |
|---|---|---|
| 1 | `role-level deny blocks role-level allow` | `addDenyResourcePermissions` precedes `addResourcePermissions`. |
| 2 | `role-level deny blocks read_all sentinel` | DENY > read_all/write_all globally. |
| 3 | `user-level deny blocks role allow` | `userDenyPerms` overrides role allow even with read_all. |
| 4 | `user-level deny is per-action` | DENY `delete` does not affect `show`/`list`. |
| 5 | `legacy replacement semantics unchanged` | `user_tb_permissions` still replaces role set without explicit deny. |
| 6 | `compiled path applies deny precedence` | When `compiledPermissions` present, deny lookup returns false first. |
| 7 | `hasExplicitDeny() public method` | Contract addition works. |

Run via:
```
php vendor/bin/phpunit unit-tests/acl
```

### 10. `make acl` regen

No code change in the command. After implementation:
```
php com make acl --force
```
regenerates `storage/security/acl.cache` with new compiled fields.

---

## Files to create

```
src/framework/Security/Compiler/EffectivePermissionCompiler.php
src/framework/Security/Compiler/TbPermissionBits.php
src/framework/Security/Explanation/PermissionExplanation.php
unit-tests/acl/AclCompiledPermissionsTest.php
unit-tests/acl/AclEngineDenyTest.php
app/Database/Migrations/<timestamp>_create_user_deny_permissions.php   (verify exact path during impl)
app/Controllers/UserDenyPermissions.php                                (ApiController resource — follow create-api-endpoint-guide)
app/Models/UserDenyPermissionsModel.php                                (or matching project pattern)
```

## Files to modify

```
src/framework/Security/Snapshot/AclSnapshot.php       (add effectiveAllows, effectiveDenies, denyRolePerms, permissionExplanations — defaults)
src/framework/Security/Domain/AclContext.php          (add compiledPermissions, userDenyPerms, userDenySpPerms, withCompiled factory)
src/framework/Security/Engine/AclEngine.php           (deny-first fast-path; legacy path deny short-circuit; reuse shared bit constants)
src/framework/Security/Contracts/AuthorizationServiceInterface.php  (append hasExplicitDeny)
src/framework/Acl.php                                 (addDenyResourcePermissions / addDenySpecialPermissions; $deny_role_perms; wire compiler into getSnapshot + has*Permission methods)
packages/boctulus/fine-grained-acl/src/Acl.php        (fetchDenyPermissions, getFreshDenyPermissions, inject into context)
```

## Files NOT changed (backward compat guarantees)

```
src/framework/Security/Contracts/AclEngineInterface.php       (still extends AuthorizationServiceInterface — picks up hasExplicitDeny transitively)
src/framework/Security/Contracts/AuthorizationPolicyInterface.php
packages/boctulus/basic-acl/src/Acl.php               (inherits new builder methods; no DB-deny loading by design)
config/acl.php.example                                (no required changes — example may be expanded to demo deny)
app/Commands/MakeCommand.php                          (no command changes)
```

## Verification

1. `php vendor/bin/phpunit unit-tests/acl` → all existing tests still green + new compiled-permission + deny tests pass.
2. `php com make acl --force --debug` → regenerates snapshot, dumps non-empty `effectiveAllows` and `denyRolePerms` (when configured).
3. Migration: run `php com migrate` (or equivalent) and confirm `user_deny_permissions` table exists.
4. API smoke test:
   ```
   POST /api/v1/user_deny_permissions  { user_id: 119, resource: "products", action: "delete" }
   GET  /api/v1/user_deny_permissions
   ```
   Then call a protected endpoint for that user and confirm 403.
5. Manual unit smoke: `acl()->hasPermission('show', 'products')` for a role with `addDenyResourcePermissions('products', ['show'])` → returns false even if role allows it.
6. Open one consuming controller (e.g., `app/Controllers/UserRoles.php`) and verify no behavior change for an existing user with no deny rows.

---

## Compatibility matrix

| Element                                  | Change                                  | Breaking |
|------------------------------------------|-----------------------------------------|----------|
| `Acl::hasPermission()`                   | Internal: compiler + engine fast path   | No       |
| `Acl::can()`                             | Internal: same                          | No       |
| `Acl::addRole()` / `addInherit()`        | Unchanged                               | No       |
| `Acl::addResourcePermissions()`          | Unchanged                               | No       |
| `Acl::addSpecialPermissions()`           | Unchanged                               | No       |
| `AclContext`                             | New fields (defaults)                   | No       |
| `AclSnapshot`                            | New fields (defaults)                   | No       |
| `AuthorizationServiceInterface`          | New method `hasExplicitDeny` (engine provides default impl) | Potential — if external implementers exist, document |
| `AclEngineInterface`                     | Extends — picks up new method           | No       |
| `BasicACL` / `FineGrainedACL`            | Inherit new builder APIs                | No       |
| `user_tb_permissions` semantics          | Same replacement semantics              | No       |
| Existing serialized snapshots            | Rehydrate with defaults                 | No       |
| Existing tests (23+)                     | Stay green                              | No       |

---

## Out of scope (deferred)

- v3 §6 **Hard Security Constraints** layer (suspended accounts, legal hold, embargo). Separate layer above business ACL.
- v3 §7 **Role hierarchy doc clarifications**. Pure documentation pass — later.
- **Auth-token payload** changes (JWT-embedded compiled permissions). Eager opt-in API is exposed; wiring into `AuthController` is a follow-up.

---

## Progress

- [x] §1 EffectivePermissionCompiler + TbPermissionBits
- [x] §2 AclSnapshot new fields
- [x] §3 AclContext new fields + withCompiled factory
- [x] §4 AclEngine deny-first fast path
- [x] §5 PermissionExplanation
- [x] §6 Builder deny APIs
- [x] §7 DB migration + UserDenyPermissions endpoint + FineGrainedACL loader
- [x] §8 Wire compiler into getSnapshot + has*Permission methods
- [x] §9 Tests (AclCompiledPermissionsTest + AclEngineDenyTest) — 58/58 green
- [x] §10 `make acl --force` regen + docs (CHANGELOG-acl.md, ACL.md)

