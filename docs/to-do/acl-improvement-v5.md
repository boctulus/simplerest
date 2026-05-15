# ACL Improvement v5 — Policy Resolution Inspector (hardened work plan)

> Enterprise-grade ACL admin UI aligned with the v4 compiled engine. Evolves
> `admin/acl-permissions` from a checkbox CRUD into a **Policy Resolution
> Inspector**: an IAM-style editor + read-only effective viewer + causality
> debugger. The frontend never computes ACL — it only renders backend-resolved
> policy.

Estado: planificado — **scope frozen** (post architectural review v5.1)

---

## 0. Architectural verdict (incorporated)

Approved against `AclSnapshot` / `AclContext` / `EffectivePermissionCompiler` /
deny precedence / compiled permissions / explainability / future IAM evolution.
This revision absorbs 12 hardening adjustments + 1 internally-found gap. No new
features beyond these. The frontend is a **policy resolution system**, not an
admin CRUD.

### Bounded contexts (3 + 1 read DTO)

```
admin/acl-permissions  (Policy Resolution Inspector)
│
├── [Tab 1] Assignments   ← edit declared rules        (writes)
├── [Tab 2] Effective     ← compiled result, readonly  (read)
└── [Tab 3] Explain       ← runtime causality, lazy     (read, audit)

Assignments tab is hydrated by ONE read DTO: GET /api/v1/acl/assignments
```

`Assignment` (declared) and `Effective` (compiled) are **separate concerns and
separate endpoints**. `Explain` answers *only* "why did it resolve to
allow/deny", never "what rules are declared" (that is `assignments`).

---

## 1. Inspector scope — what this inspector covers (CRITICAL, must be visible)

The v4 engine resolves more than this inspector models. The inspector must
**not lie**: every read-only panel renders a persistent scope disclaimer.

| Layer | In scope (v5) |
|---|---|
| Role resource permissions (`rolePerms`) | ✅ |
| Role special permissions / capabilities | ✅ |
| `user_tb_permissions` (replacement) | ✅ |
| `user_sp_permissions` (additive) | ✅ |
| `user_deny_permissions` (resource deny) | ✅ |
| `user_deny_sp_permissions` (capability deny) | ✅ (new table, §3) |
| `read_all` / `write_all` wildcard sentinel | ✅ |
| Folder permissions (`folder_permissions`, `folder_other_permissions`) | ❌ NOT modeled |
| Ownership-derived access (`belongs_to`) | ❌ NOT modeled |
| Row-level / attribute-level permissions | ❌ NOT modeled |

**Disclaimer banner (Tab 2 + Tab 3), fixed text:**

> Effective decisions shown are pre-folder and pre-ownership. A resource shown
> as `deny` here may still be reachable via a shared folder or ownership; a
> resource shown as `allow` may still be filtered at row level. This inspector
> covers role + user policy layers only.

Documented in `docs/ACL.md` as the canonical inspector scope statement.

---

## 2. Concurrency model — two tokens (replaces single `permission_hash`)

`snapshot_version` alone is **insufficient**: it changes only when `acl.php` is
recompiled. Per-user tables (`user_roles`, `user_tb_permissions`,
`user_sp_permissions`, `user_deny_permissions`, `user_deny_sp_permissions`)
change the effective result **without** moving `snapshot_version`.

| Token | Scope | Changes when | Use |
|---|---|---|---|
| `snapshot_version` | Global | `php com make acl --force` (acl.cache regenerated) | Display + fast stale detection |
| `acl_context_hash` | Per-user compiled context | snapshot **or** any per-user assignment row changes | **Authoritative concurrency guard** |

- **Rename**: `permission_hash` → `acl_context_hash` everywhere. Rationale:
  `same effective != same ACL state` (wildcard-allow and explicit-allow can
  yield identical effective output from different declared state). The hash must
  identify the **compiled input context**, not the effective output.
- `acl_context_hash = "aclv4:" . sha1(canonical_json)` where `canonical_json`
  is the deterministic serialization of:
  `{ snapshot_version, user_id, roles[sorted], userSpPerms[sorted],
  userTbPerms{resource:packed, sorted}, userDenyPerms[sorted],
  userDenySpPerms[sorted] }`.
- `snapshot_version`: derived from the snapshot artifact (sha1 of
  `storage/security/acl.cache` truncated, or its mtime epoch). Stable across
  requests until `make acl` runs. Display-only.
- **Save guard (Tab 1):** every write sends the `acl_context_hash` it was
  rendered from. Backend recomputes the hash from current DB+snapshot state. On
  mismatch → `409 Conflict` + `{ error, current_acl_context_hash }`; UI shows a
  non-destructive "ACL state changed — reload" banner and refuses the write.
- Optional human-readable `acl_state_version` (monotone integer per user) is
  **out of scope**; the hash is the guard. Documented as a future ergonomic add
  only if audit needs a readable counter.

---

## 3. Files involved

| Acción | Archivo |
|---|---|
| Modificar | `docs/ACL.md` (inspector scope, endpoints, origin/effect model, concurrency) |
| Crear | `app/Controllers/Api/AclInspector.php` |
| Crear | `src/framework/Security/Explanation/AclResolutionExplainer.php` (pure, §6) |
| Crear | `database/migrations/<timestamp>_user_deny_sp_permissions.php` |
| Modificar | `packages/boctulus/fine-grained-acl/src/Acl.php` (`fetchDenySpPermissions`) |
| Modificar | `config/routes.php` (5 rutas) |
| Modificar | `app/Views/admin/acl_permissions.php` (700 líneas → 3 tabs) |
| Crear | `unit-tests/acl/AclResolutionExplainerTest.php` |

---

## Fase 1 — Docs (`docs/ACL.md`)

Replace "Posible FrontEnd para los roles y permisos" with:

- Concept: Policy Resolution Inspector (not a checkbox editor).
- 3 bounded contexts (Assignments / Effective / Explain) + the `assignments`
  read DTO.
- **Inspector scope statement** (§1 table + disclaimer text) — canonical.
- Spec of the **5** endpoints with typed DTOs (§5).
- `origin` + `effect` resolution model (§6) — explicitly *not* a fused enum.
- Concurrency model: `snapshot_version` (display) + `acl_context_hash`
  (authoritative) — §2.
- Capability namespace note (future `system.read_all`) — documented, not built.
- Resource registry note: **eventually dynamic** (§5.5) — not a closed universe.

---

## Fase 2 — DB: `user_deny_sp_permissions`

The v4 engine + `EffectivePermissionCompiler::compileForUser()` already accept
`userDenySpPerms` and `AclContext` already carries the field — but there is **no
DB table or loader** for it (v4 only shipped `user_deny_permissions`). This
closes the deny symmetry.

```sql
CREATE TABLE user_deny_sp_permissions (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    user_id     INT NOT NULL,
    sp_name     VARCHAR(100) NOT NULL,
    created_by  INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by  INT NULL,
    updated_at  TIMESTAMP NULL,
    UNIQUE KEY uq_user_sp_deny (user_id, sp_name),
    INDEX idx_user (user_id)
);
```

| Tipo | Allow | Deny |
|---|---|---|
| Resource | `user_tb_permissions` | `user_deny_permissions` |
| Capability (sp) | `user_sp_permissions` | `user_deny_sp_permissions` ← new |

`FineGrainedACL` adds `fetchDenySpPermissions(int $uid): array` →
`['sp_name' => true]`, injected into `AclContext->userDenySpPerms` mirroring the
existing `fetchUserDenyPerms()` plumbing. `BasicACL` unaffected (no fresh
fetchers, by design).

Crear migración con `php com make` (verificar grupo: `php com make help`),
ejecutar con `php com migrations migrate`.

---

## Fase 3 — Backend: `AclInspector` controller

**Archivo:** `app/Controllers/Api/AclInspector.php`

### Hard rule: NO duplicar lógica del engine

The controller loads data and delegates to `AclContext::withCompiled()`. It
never expands `read_all`, merges roles, or interprets bitmasks. Causality
attribution lives in the pure `AclResolutionExplainer` (§6), not in the
controller.

### Central helper (reused by effective + explain)

```php
protected function buildCompiledContext(int $uid): AclContext
{
    // Load: roles, user_tb_perms, user_sp_perms,
    //       user_deny_permissions, user_deny_sp_permissions
    // return AclContext::withCompiled(
    //   snapshot:        acl()->getSnapshot(),
    //   compiler:        new EffectivePermissionCompiler(),
    //   userId:          $uid,
    //   roles:           [...], authenticated: true,
    //   userSpPerms:     [...], userTbPerms:     [...],
    //   userDenyPerms:   [...], userDenySpPerms: [...],
    // );
}
```

`acl_context_hash` and `snapshot_version` are computed once here and attached to
every response (`effective`, `explain`, `assignments`).

---

## 4. Endpoint specs

### 4.1 `assignments()` — GET /api/v1/acl/assignments?user_id=X

Requiere: `grant`. **New endpoint** (critique #2 — declared state is not mixed
into `explain`).

Returns the **declared** (editable) state only — what Tab 1 edits:

```json
{
  "roles":           [{ "id": 100, "name": "supervisor" }],
  "user_sp_perms":   ["impersonate"],
  "user_deny_sp_perms": ["transfer"],
  "user_tb_perms":   { "products": { "show": 1, "list": 1, "delete": 0 } },
  "user_deny_perms": { "products": ["delete"] },
  "acl_context_hash": "aclv4:8f2ab1...",
  "snapshot_version": 42
}
```

`explain` and `effective` MUST NOT echo declared rules — they return causality /
compiled result respectively.

### 4.2 `effective()` — GET /api/v1/acl/effective?user_id=X

Requiere: `grant`. **Grouped DTO, not a flat array** (critique #1). The UI is
accordion-by-resource; the DTO mirrors that so the frontend never `groupBy`s.

```json
{
  "resources": {
    "products": {
      "create": { "result": "allow", "origin": "WILDCARD", "source": "write_all", "inherited": true,  "conflict": false },
      "delete": { "result": "deny",  "origin": "USER",     "source": "products.delete", "inherited": false, "conflict": true }
    },
    "users": {}
  },
  "capabilities": {
    "impersonate": { "result": "allow", "origin": "USER", "source": "user_sp_permissions" },
    "transfer":    { "result": "deny",  "origin": "USER", "source": "user_deny_sp_permissions" }
  },
  "acl_context_hash": "aclv4:8f2ab1...",
  "snapshot_version": 42,
  "snapshot_generated_at": "2026-05-15T11:20:00Z"
}
```

- Resource map keys are real resources only. The compiler's internal `'__sp__'`
  and `'*'` sentinel keys are **never leaked**; sp data is surfaced under
  `capabilities`, wildcard is reflected via `origin: "WILDCARD"` on the affected
  resource/action.
- `result` ∈ `allow|deny`; `origin`/`effect` per §6; `conflict` true when an
  allow layer and a deny layer both matched (deny won).

### 4.3 `explain()` — GET /api/v1/acl/explain?user_id=X&resource=Y&action=Z

Requiere: `grant`. `resource` + `action` **mandatory** (no all-mode — avoids
CPU/JSON explosion). Missing either → `400`.

Returns **runtime causality only** (no declared summary — that is
`assignments`):

```json
{
  "resource": "products",
  "action": "delete",
  "result": "deny",
  "resolution_path": [
    { "origin": "ROLE",     "effect": "allow", "source": "supervisor" },
    { "origin": "WILDCARD", "effect": "allow", "source": "write_all" },
    { "origin": "USER",     "effect": "deny",  "source": "products.delete" }
  ],
  "final_decision": "deny",
  "has_conflict": true,
  "acl_context_hash": "aclv4:8f2ab1...",
  "snapshot_version": 42,
  "snapshot_generated_at": "2026-05-15T11:20:00Z"
}
```

**Forward-compat (not implemented):** the resolver (§6) is a pure function of
`(context, resource, action)`. A future `POST /api/v1/acl/explain/batch`
("explain all denies / all conflicts") can fan out over the same resolver with
zero engine changes. Documented; out of scope for v5.

### 4.4 `capabilities()` — GET /api/v1/acl/capabilities

Requiere: autenticado (internal metadata, no `grant`). Returns
`snapshot->validSpPerms`. Flat names today (`read_all`); namespaced future
(`system.read_all`) documented only.

### 4.5 `resources()` — GET /api/v1/acl/resources

Requiere: autenticado. Returns the **currently known** union:

1. `tb_permissions` keys across all roles in the snapshot
2. `SELECT DISTINCT tb FROM user_tb_permissions`
3. `SELECT DISTINCT resource FROM user_deny_permissions`

```json
{ "resources": ["products", "users"], "is_partial": true }
```

`is_partial: true` is **always** set: the resource registry is eventually
dynamic. Folder resources, dynamically registered resources, and future
row-level namespaces are **not** enumerable here. The UI must treat this as a
hint list (autocomplete seed), never a closed universe. Documented in
`docs/ACL.md`.

### 4.6 Rutas — `config/routes.php`

```php
WebRouter::get('api/v1/acl/assignments',  [AclInspector::class, 'assignments']);
WebRouter::get('api/v1/acl/effective',    [AclInspector::class, 'effective']);
WebRouter::get('api/v1/acl/explain',      [AclInspector::class, 'explain']);
WebRouter::get('api/v1/acl/capabilities', [AclInspector::class, 'capabilities']);
WebRouter::get('api/v1/acl/resources',    [AclInspector::class, 'resources']);
```

---

## 6. `origin` + `effect` model + `AclResolutionExplainer` (pure)

### 6.1 Why not a fused `ExplainType`

A single enum (`ROLE_ALLOW`, `USER_DENY`, …) conflates **origin** and
**effect** and does not extend (it explodes into `GROUP_DENY`, `TENANT_ALLOW`,
`FOLDER_ALLOW`, `POLICY_ALLOW`). Model them orthogonally:

| `origin` | `effect` |
|---|---|
| `ROLE` | `ALLOW` |
| `USER` | `DENY` |
| `WILDCARD` | |
| `DEFAULT` | |

(Reserved future origins: `GROUP`, `TENANT`, `FOLDER`, `POLICY` — no code now.)
This maps cleanly onto the existing `PermissionExplanation`
(`source` → origin, `granted` → effect, `mode` → modifier), so **no new
conflicting enum is introduced**. The proposed v5.0 `ExplainType` is dropped.

### 6.2 Gap closed: `compileForUser()` emits no explanations

Verified in `EffectivePermissionCompiler`: only `compileRoles()` builds
`permissionExplanations` (per-role, snapshot). `compileForUser()` returns just
`{allow, deny}` maps — **no per-user causality**. So `explain()` cannot read a
ready-made trace; without a defined source it would re-implement engine
precedence inside the controller (forbidden).

**Solution — `AclResolutionExplainer` (pure class, no DB, no `auth()`):**

`src/framework/Security/Explanation/AclResolutionExplainer.php`
Namespace: `Boctulus\Simplerest\Core\Security\Explanation`

```php
final class AclResolutionExplainer
{
    /** Read-only ATTRIBUTION over the already-compiled artifact + raw inputs.
     *  Does NOT re-decide; it explains the decision the engine already made,
     *  walking the v4 precedence order:
     *    deny[resource][action] → deny['*'] → allow['*'] → allow[resource]
     *  and attributing each matched layer to ROLE | USER | WILDCARD | DEFAULT.
     */
    public function explain(
        AclContext  $context,   // already withCompiled()
        AclSnapshot $snapshot,
        string      $resource,
        string      $action
    ): array; // { result, resolution_path[], final_decision, has_conflict }
}
```

It inspects `context->compiledPermissions`, `context->userDenyPerms`,
`context->userDenySpPerms`, `context->userTbPerms`, and
`snapshot->denyRolePerms` / `snapshot->rolePerms` to label *which input*
produced each matched layer. This honors "no duplicar lógica del engine": it
attributes over the compiled output, it does not recompute authorization.
Pure + unit-testable exactly like `EffectivePermissionCompiler`
(`unit-tests/acl/AclResolutionExplainerTest.php`). `effective()` reuses the same
explainer per cell to fill `origin`/`source`/`conflict`.

---

## Fase 4 — Frontend: fix critical bug first

### Bug: `updateTbPermission` destructivo

Sends 1 field → replacement wipes the rest of the resource's perms.

```js
function buildTbPayload(tbName, userId) {
    const payload = { user_id: userId, tb: tbName };
    document.querySelectorAll(`[data-table="${tbName}"].tb-perm-checkbox`)
        .forEach(el => { payload[el.dataset.field] = el.checked ? 1 : 0; });
    return payload;
}
```

Must ship before any tab work (Tab 1 depends on correct write semantics).

---

## Fase 5 — Frontend: 3-tab architecture + normalized store

### AclStore (vanilla JS module) — normalized indexing (critique #8)

```js
const AclStore = {
    // Concurrency
    snapshotVersion:  null,
    aclContextHash:   null,   // authoritative guard (was permissionHash)

    // Metadata
    selectedUserId: null,
    roles: [], capabilities: [], resources: [],   // resources = hint list only

    // Assignments (editable — from GET /acl/assignments)
    userRoles: [], userSpPerms: [], userDenySpPerms: [],
    userTbPerms: {},      // { [resource]: { [field]: 0|1 } }
    userDenyPerms: {},    // { [resource]: [action] }

    // Effective (readonly, normalized — never an array)
    effective: {          // { [resource]: { [action]: dto } }
        resources: {},
        capabilities: {},
    },

    // Explain (lazy cache)
    explanations: {},     // { "resource:action": dto }

    // Dirty state
    unsavedChanges: {},   // { "resource": true }
};
```

No `groupBy` at render time — store mirrors the backend grouped DTO.

### IAM-style terminology (critique #12) — applied across all tabs

| Old (CRUD) | New (IAM) |
|---|---|
| Special permissions | **Capabilities** |
| Resource permissions | **Resource Policies** |
| Overrides | **Policy Overrides** |
| (page) | **Policy Resolution Inspector** |

---

### Tab 1 — Assignments (edit declared rules only)

Hydrated by `GET /api/v1/acl/assignments`. Does **not** show effective perms.

- **Roles**: Tom Select multiselect, async search, removable tags.
  (Tom Select is the chosen lib — no Select2.)
- **Capabilities — tri-state** per capability:

  | Estado | Fuente | Visual | Editable |
  |---|---|---|---|
  | inherited | role | chip muted | No |
  | allow | `user_sp_permissions` | chip naranja + [×] | Sí |
  | deny | `user_deny_sp_permissions` | chip rojo + [×] | Sí |

  Actions: [Allow] / [Deny] / [Reset]. No binary checkbox.
- **Resource Policies — mode-first**:

  ```
  products
    Mode: (•) Inherited   ( ) Policy Override
  ```

  Override active →
  - Warning: `⚠ This Policy Override replaces ALL inherited permissions for this resource`
  - Diff preview before save:
    ```
    Before (inherited): show  list  update
    After  (override):  show  list  delete
    ```
  - Checkboxes = replacement set
  - Sub-section "Explicit Denies" → CRUD over `user_deny_permissions`
- **Dirty state**: `Unsaved changes` banner until save/discard.
- **Concurrency**: save sends `acl_context_hash`. Backend recompute mismatch →
  `409` + reload banner; write refused (§2).

---

### Tab 2 — Effective (readonly) + filters from day 1 (critique #7)

Loads `GET /api/v1/acl/effective?user_id=X`. Renders the **scope disclaimer
banner** (§1) at the top — non-dismissible.

**Filter bar (mandatory, not deferred):**

- toggle: show only conflicts
- toggle: show only denies
- toggle: show only overrides (`origin: USER` on a resource)
- text: search resource
- text: search action

Accordion per resource (lazy-expand). Capabilities rendered in their own
section (from `effective.capabilities`).

| action | result | origin | inherited | conflict |
|---|---|---|---|---|
| create | allow | role | true | false |
| delete | deny | user | false | true ⚠ |
| update | allow | wildcard | true | false |

Cell colors:

| Estado | Color | Nota |
|---|---|---|
| inherited allow | verde claro | |
| direct allow | verde fuerte | |
| wildcard allow | azul | tooltip: "global allow, may still be overridden by deny" |
| deny | rojo | |
| conflict `⚠` | icono sobre color | |
| absent | gris | |

Footer — **informational only** (critique #9, `[Regenerate]` removed):

```
ACL Snapshot v42 · Generated 2026-05-15 11:20 · context aclv4:8f2ab1…
```

> `Regenerate` is intentionally absent. Recompiling the global ACL invalidates
> caches for **all** users and is a heavy operation. If ever exposed it must be
> superadmin-only + confirm modal + async job — out of v5 scope. Use
> `php com make acl --force` from CLI.

---

### Tab 3 — Explain (lazy, audit) — runtime causality only

Loads `GET /api/v1/acl/explain?user_id=X&resource=Y&action=Z` on Tab-2 cell
click. Cached in `AclStore.explanations`. Renders the scope disclaimer banner.

Shows:

- **Effective result** — ALLOW / DENY highlighted
- **Resolution Timeline** — `resolution_path[]`, icon keyed by `origin`+`effect`
- **Conflict banner** — `has_conflict` → `⚠ Conflicting policies resolved by deny precedence`
- **Snapshot metadata** — `snapshot_version` + `snapshot_generated_at`

No "Declared summary" here (that lives in `assignments` / Tab 1).

Icon map (keyed by origin+effect, extensible):

| origin / effect | Icon FA |
|---|---|
| ROLE / ALLOW | fa-user-check verde |
| ROLE / DENY | fa-user-times rojo |
| USER / ALLOW | fa-user-pen naranja |
| USER / DENY | fa-ban rojo |
| WILDCARD / ALLOW | fa-globe azul |
| WILDCARD / DENY | fa-globe rojo |
| DEFAULT / DENY | fa-lock gris |

---

## Orden de ejecución

```
1.  docs/ACL.md (scope, 5 endpoints, origin/effect, concurrency model)
2.  Migración user_deny_sp_permissions
3.  FineGrainedACL::fetchDenySpPermissions() + context injection
4.  AclResolutionExplainer (pure) + AclResolutionExplainerTest
5.  AclInspector controller (5 endpoints) + rutas
6.  Fix updateTbPermission (critical bug)
7.  AclStore (normalized module)
8.  Layout: 3 tabs + scope disclaimer banners
9.  Tab 1: assignments load + Roles (Tom Select)
10. Tab 1: Capabilities tri-state
11. Tab 1: Resource Policy override mode + dirty state + diff preview
12. Tab 1: Explicit denies CRUD + 409 concurrency guard
13. Tab 2: Effective accordion (readonly) + filter bar
14. Tab 3: Explain panel (lazy, causality only)
```

---

## Verificación

1. `php com make acl --force` → snapshot regenerado, `snapshot_version` cambia.
2. `GET /acl/capabilities` → lista sin requerir `grant`.
3. `GET /acl/resources` → unión snapshot+DB, `is_partial: true` siempre.
4. `GET /acl/assignments?user_id=X` → declared state; **no** causality.
5. Marcar/desmarcar checkbox → no borra otros permisos del recurso (bug fix).
6. `GET /acl/effective?user_id=X` → DTO **agrupado** (`resources`+`capabilities`),
   sin claves `__sp__`/`*`, con `origin`, `conflict`, `acl_context_hash`.
7. `GET /acl/explain?user_id=X&resource=products&action=delete` → `resolution_path`
   con `{origin, effect}` separados, `has_conflict: true`. Sin declared summary.
8. `GET /acl/explain?user_id=X` (sin resource/action) → `400`.
9. Usuario `write_all` + `user_deny_permissions(products.delete)` → celda roja
   `⚠`, explain: `WILDCARD/allow` + `USER/deny` + conflicto.
10. Capability deny: `user_deny_sp_permissions(transfer)` → capability roja en
    Tab 2, explain `USER/deny`.
11. Concurrency: modificar `user_tb_permissions` del usuario en otra pestaña →
    save en Tab 1 → `409` + banner reload (snapshot_version intacto, hash cambió).
12. Tab 2 filtros: only-conflicts / only-denies / only-overrides / search
    resource / search action — todos operativos.
13. Scope disclaimer visible y no descartable en Tab 2 y Tab 3.
14. `php vendor/bin/phpunit unit-tests/acl` → verde, incluye
    `AclResolutionExplainerTest`.

---

## Out of scope (documented, not implemented)

- `acl_state_version` readable per-user counter (hash is the guard).
- Batch explain (`POST /acl/explain/batch`) — resolver is batch-ready.
- Folder / ownership / row-level / attribute inspector layers.
- Capability namespacing (`system.read_all`).
- `Regenerate` UI affordance.
- Any feature beyond these 14 steps. **Scope frozen.**
