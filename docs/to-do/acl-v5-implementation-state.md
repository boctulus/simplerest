# ACL v5 — Implementation State (pause point)

Estado: **backend completo + verificado por curl · UI reescrita y renderiza · falta test UI en navegador**

Fecha pausa: 2026-05-15 · git base: d349e0b8b

---

## Decisión arquitectónica

**Módulo** (no package): `app/Modules/Security`. El inspector es funcionalidad
de app acoplada a `app/Pages/admin` + `config/acl.php`; los packages quedan para
basic-acl / fine-grained-acl (extensiones reutilizables del engine).

---

## Hecho ✅

### Módulo `app/Modules/Security`
- `src/ModuleProvider.php` — carga `config/routes.php`.
- `config/routes.php` — 5 GET: `api/v1/acl/{assignments,effective,explain,capabilities,resources}`.
- Registrado en `config/config.php` → `providers[]` (tras Xeni ModuleProvider).
- `src/AclResolutionExplainer.php` — clase pura. Atribución read-only sobre el
  artefacto ya compilado (no re-decide). `origin`(ROLE/USER/WILDCARD/DEFAULT) +
  `effect`(ALLOW/DENY) + `decisive` (capa que determinó el fallo) +
  `explainCapability()`.
- `src/Controllers/AclInspectorController.php` — extiende `ResourceController`
  (necesario: bootstrapea `auth()->check()` para que `acl()` refleje al caller;
  patrón de `DumbAuthController`, `global $api_version='v1'`). Loaders per-user
  desde DB (roles/sp/tb/deny), `acl_context_hash` + `snapshot_version` + scope,
  guard `grant` (o admin-or-higher), dedupe de `validSpPerms` (la tabla
  `sp_permissions` está seedeada ~17×).

### Verificación backend (curl, OK)
- `assignments` 200 — roles/sp/tb/deny + hashes.
- `effective` 200 — DTO agrupado; `products.delete` → deny, origin USER
  (decisive), conflict true; show/list/create → WILDCARD (admin read/write_all).
- `explain` 200 — resolution_path + decisive + has_conflict; sin params → 400.
- `capabilities` 200 — 12 items deduped.
- `resources` 200 — `is_partial:true`.
- Guard: sin grant → 403.

### UI `app/Views/admin/acl_permissions.php` (reescrita)
- Bootstrap 5 + jQuery (template ya los carga; JS en `DOMContentLoaded`).
- Selector por **User ID numérico** (`/api/v1/users` es un stub: no hay
  búsqueda por nombre real).
- 3 tabs: **Assignments** (roles read-only, capabilities tri-state,
  Resource Policies con **fix del bug** `buildTbPayload` enviando TODOS los
  checkboxes, explicit denies CRUD), **Effective** (scope banner, filtros
  conflicts/denies/overrides/search, accordion por recurso, capabilities,
  footer informativo sin Regenerate), **Explain** (timeline origin/effect con
  iconos, decisive resaltado, conflict banner). Concurrency: `guardThen()`
  re-lee assignments y compara `acl_context_hash` antes de cada mutación.
- Render OK: `GET /admin/acl-permissions` → HTTP 200, sin errores PHP.

---

## Pendiente ⏳

1. **Test UI en navegador** (task #5) — abrir http://simplerest.lan/admin/acl-permissions
   logueado como admin con `grant`; validar:
   - cargar user 133, ver 3 tabs.
   - Resource Policy: marcar/guardar → NO borra otros permisos (bug fix).
   - capability Allow/Reset; add/remove explicit deny.
   - Effective filtros; click celda → Explain prefilled.
   - Concurrency: cambiar algo en otra pestaña → banner reload.
2. **Fase 2 v5 diferida**: migración `user_deny_sp_permissions` +
   `FineGrainedACL::fetchDenySpPermissions()`. El engine y el controller ya
   soportan el shape (`userDenySpPerms`); el controller hace try/catch si la
   tabla no existe. Capability **deny** no editable en UI hasta crear la tabla.
3. **Doc**: actualizar `docs/ACL.md` (scope inspector, 5 endpoints,
   origin/effect, concurrency) y `docs/CHANGELOG-acl.md`. Marcar Progress en
   `docs/to-do/acl-improvement-v5.md`.

---

## Datos de prueba creados (test data, borrables)

- Usuario **133** `acltester@test.local` / pass `Test1234!`
  (username `acltester`). Roles: registered(2)+admin(1000). sp: grant(9).
  `user_tb_permissions` products(show,list,create). `user_deny_permissions`
  products.delete.
- Token admin fresco en `scripts/tmp/tok.txt` (para reanudar curl). Re-login:
  `POST /api/v1/auth/login {"email":"acltester@test.local","password":"Test1234!"}`.

---

## Cómo reanudar

1. `T=$(cat scripts/tmp/tok.txt)` → curl con `Authorization: Bearer $T`.
2. Abrir la página en navegador (admin logueado) y ejecutar checklist task #5.
3. Si OK: crear migración Fase 2, actualizar docs, commit.

Archivos clave:
- `app/Modules/Security/src/Controllers/AclInspectorController.php`
- `app/Modules/Security/src/AclResolutionExplainer.php`
- `app/Views/admin/acl_permissions.php`
- Plan: `docs/to-do/acl-improvement-v5.md`
