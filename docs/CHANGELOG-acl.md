# ACL Changelog

## v5 — 2026-05-20

### Fixed

**sp_permissions duplicados eliminados**
- Se detectaron y eliminaron 216 filas duplicadas en `sp_permissions`.
- Se agregó constraint `UNIQUE` en la columna `name` para prevenir duplicados futuros.

### Changed

**Split de `user-roles` en dos comandos (`php com acl`)**
- `list-user-roles` — lista **todos** los usuarios con sus roles. Acepta `--role=<nombre>` para filtrar por rol y `--role=null` para usuarios sin rol asignado. Alias: `ls-user-roles`.
- `show-user-roles` — muestra los roles de **un usuario específico**. Requiere `--email` o argumento posicional. Alias: `show-ur`.
- El antiguo comando `user-roles` (que sólo hacía una de las dos cosas) fue eliminado.

**Confirmación requerida en operaciones destructivas**
- `clear-tb` y `replace-tb` ahora exigen el flag `--force` para ejecutarse.
- Sin `--force`, el comando imprime un resumen de lo que haría y termina sin modificar datos.
- `--dry-run` sigue disponible para previsualizar la operación sin alteraciones.

---

## v4 — 2026-05-15

### Added

**Compiled effective permissions infrastructure**
- `src/framework/Security/Compiler/EffectivePermissionCompiler.php` — pure compiler. `compileRoles()` produces per-role effective allows + denies + explanations; `compileForUser()` produces a per-user O(1) hash map fed into the engine fast path.
- `src/framework/Security/Compiler/TbPermissionBits.php` — single source of truth for the TB permission bitmask (`SHOW`, `LIST`, …) and `unpack()` / `unpackGranted()` helpers.
- `src/framework/Security/Explanation/PermissionExplanation.php` — deterministic per-permission explanation DTO. Source + mode are emitted into `AclSnapshot::$permissionExplanations` for the admin frontend.

**Business-level deny rules (DENY > ALLOW)**
- Builder API on `Acl`:
  - `addDenyResourcePermissions(string $table, array $actions, $to_role = null)` — supports the same `read` / `write` shorthand expansion as `addResourcePermissions`.
  - `addDenySpecialPermissions(array $sp_permissions, $to_role = null)`.
- Snapshot: new `denyRolePerms` field exposes role-level explicit denies for the runtime engine.
- Context: new `userDenyPerms` + `userDenySpPerms` fields carry user-level explicit denies into the engine.
- Engine: `hasExplicitDeny()` (public, on `AuthorizationServiceInterface`) — short-circuits before any ALLOW resolution. Denies beat `read_all` / `write_all` sentinels.

**DB + endpoint**
- Migration: `database/migrations/2026_05_15_180000000_user_deny_permissions.php` creates `user_deny_permissions(id, user_id, resource, action, …)` with `UNIQUE(user_id, resource, action)` and FK to users.
- Controller: `app/Controllers/Api/UserDenyPermissions.php` — GET/POST/DELETE under `/api/v1/user_deny_permissions`. POST replaces any prior row matching the (user, resource, action) tuple to keep the table single-source-of-truth.
- Authorization for the new endpoint: requires the `grant` special permission.
- FineGrainedACL package: `getFreshDenyPermissions($uid)` plus override of base hook `fetchUserDenyPerms()` to inject DB-backed denies into the runtime context.

**Tests**
- `unit-tests/acl/AclCompiledPermissionsTest.php` — 9 tests covering compiler output, fast path, replacement semantics with `compiledPermissions`, snapshot rehydration with default fields.
- `unit-tests/acl/AclEngineDenyTest.php` — 7 tests covering role-level deny, user-level deny, deny > read_all sentinel, per-action granularity, legacy replacement semantics intact, `hasExplicitDeny()` contract.
- Final run: 58/58 tests, 90 assertions, all green.

### Changed

- `AclSnapshot` now exposes `effectiveAllows`, `effectiveDenies`, `denyRolePerms`, `permissionExplanations` (all default to `[]` — old serialized cache files rehydrate without breakage).
- `AclContext` now exposes `compiledPermissions` (defaults to `null`), `userDenyPerms`, `userDenySpPerms`. New static factory `AclContext::withCompiled()` builds a context with O(1) lookup pre-baked.
- `AclEngine::hasPermissionInternal()`, `hasSpecialPermissionInternal()`, and `hasResourcePermission()` gain a compiled-permission fast lane (deny first, then `*` sentinel, then resource map). Legacy paths still work and now also honor `hasExplicitDeny()` at the top.
- `Acl::getSnapshot()` compiles per-role allows/denies/explanations on every call. `Acl::hasPermission()` / `hasSpecialPermission()` / `hasResourcePermission()` / `hasAllPermissions()` / `hasAnyPermission()` / `satisfiesPolicy()` now build contexts via `AclContext::withCompiled()`.

### Semantic deviation from v3 design doc

The v3 design document stated:

> El sistema NO adopta DENY > ALLOW global semantics como modelo principal de autorización.

This iteration **relaxes that rule** by accepting an explicit, admin-controllable DENY layer at both the builder and the DB level. The compiler's internal `effectiveDenies` (from replacement semantics) and the new explicit deny rules merge into a single deny map evaluated before any ALLOW. Replacement semantics for `user_tb_permissions` are unchanged — they continue to work as before for users with no explicit deny rows.

### Backward compatibility

| Element | Status |
|---|---|
| `Acl::hasPermission()`, `Acl::can()` | Same signature, same results for legacy inputs. |
| `Acl::addRole()`, `addInherit()`, `addResourcePermissions()`, `addSpecialPermissions()` | Unchanged. |
| `BasicACL`, `FineGrainedACL` | Inherit new builder methods automatically. `FineGrainedACL` opts into DB-backed deny loading. |
| Existing serialized `acl.cache` | Rehydrates with default values for new snapshot fields. `php com make acl --force` regenerates with the compiled payload populated. |
| Existing 23+ ACL tests | Unchanged. All green. |
| `AuthorizationServiceInterface` | Adds `hasExplicitDeny()`. `AclEngine` (the only known implementer) supplies it. External implementers must add the method. |

### Out of scope (deferred)

- v3 §6 Hard security constraints (suspended accounts, legal hold, embargo). Will sit as a separate layer above business ACL.
- v3 §7 Role-hierarchy doc clarifications.
- Auth-token payload changes (JWT-embedded compiled permissions). The eager API is exposed; wiring into `AuthController` is a follow-up.
