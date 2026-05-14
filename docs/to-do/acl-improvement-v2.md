# ACL Improvement v2 — Deny Semantics Evolution

## Objetivo

Evolucionar el sistema ACL de **replacement semantics** (override reemplaza set completo) a **policy-based deny semantics** (ALLOW + DENY + precedencia) sin romper la API pública existente.

---

## Principio Arquitectónico

La API pública (`Acl`, `AuthorizationServiceInterface`) **no cambia**. La evolución es interna en el motor de evaluación.

```
API PÚBLICA (estable)
    hasPermission(), can(), hasSpecialPermission()
        │ delega a
CAPA DE ADAPTACIÓN (nueva)
    Traduce override legacy → reglas ALLOW/DENY
        │ evalúa con
POLICY ENGINE (nuevo núcleo)
    PolicyRule con precedencia formal
```

---

## Fase 1: Foundation — Data Model

### Archivos a crear

```
src/framework/Security/Policy/
├── RuleEffect.php
├── PolicyRule.php
└── RuleCollection.php

src/framework/Security/Adapter/
└── LegacyRuleAdapter.php
```

### 1.1 RuleEffect (enum)

```php
namespace Boctulus\Simplerest\Core\Security\Policy;

enum RuleEffect: string {
    case ALLOW = 'allow';
    case DENY  = 'deny';
}
```

### 1.2 PolicyRule (value object)

```php
final class PolicyRule
{
    public function __construct(
        public readonly RuleEffect $effect,
        public readonly string     $resource,
        public readonly array      $actions,
        public readonly int        $precedence,
        public readonly ?string    $source = null,
    ) {}
}
```

### 1.3 RuleCollection

Colección de reglas con resolución por precedencia. Método `firstMatch(action, resource): ?PolicyRule`.

### 1.4 LegacyRuleAdapter

Convierte el override actual (`user_tb_permissions` bitmask) a `RuleCollection`:
- ALLOW para permisos presentes
- DENY para permisos ausentes
- Precedencia 1000 (gana sobre roles = 100)

---

## Fase 2: AclSnapshot — New Fields

### Modificar `AclSnapshot`

Agregar (con defaults compatibles):

```php
public readonly array $denyRolePerms  = [],
```

### Modificar `AclContext`

Agregar:

```php
public readonly array $userDenyPerms  = [],  // ['resource' => ['action1', 'action2']]
```

Sin cambiar firmas existentes — valores default `[]`.

---

## Fase 3: AclEngine — New Inner Evaluation

### Modificar `AclEngine`

Nuevo método privado `evaluate()` usado por todos los métodos públicos (`can`, `hasPermission`):

```
1. DENY user-level        → if denied, return false
2. DENY role-level        → if denied, return false
3. ALLOW special perms    → if read_all/write_all, return true
4. ALLOW user_tb_override → if packed perms match, return true
5. ALLOW role perms       → if role has permission, return true
6. Default                → return false
```

Los métodos públicos **no cambian su firma**.

---

## Fase 4: Builder — Add Deny Methods

### Modificar `Acl` (abstract)

Nuevos métodos builder:

```php
public function addDenyResourcePermissions(string $table, array $actions, $to_role = null);
public function addDenySpecialPermissions(array $sp_permissions, $to_role = null);
```

Se almacenan en `$deny_role_perms` (serializable).

---

## Fase 5: Contratos — Extensiones Opt-In

### Modificar `AuthorizationServiceInterface`

SOLO agregar al final:

```php
public function hasExplicitDeny(AclContext $context, string $action, string $resource): bool;
```

`AclEngineInterface` (extends `AuthorizationServiceInterface`) no tocar.

---

## Fase 6: DB + API Endpoints

### Tabla

```sql
CREATE TABLE user_deny_permissions (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    user_id     INT NOT NULL,
    resource    VARCHAR(100) NOT NULL,
    action      VARCHAR(50) NOT NULL,
    created_by  INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_res_action (user_id, resource, action)
);
```

### Endpoints

```
GET    /api/v1/user_deny_permissions
POST   /api/v1/user_deny_permissions   { user_id, resource, action }
DELETE /api/v1/user_deny_permissions/{id}
```

---

## Fase 7: FineGrainedACL — Deny from DB

Modificar `packages/boctulus/fine-grained-acl/src/Acl.php`:
- Cargar `user_deny_permissions` al construir contexto
- Proveer `getFreshDenyPermissions()`

---

## Fase 8: Tests

### Nuevo archivo: `unit-tests/acl/AclEngineDenyTest.php`

| # | Test | Verifica |
|---|------|----------|
| 1 | Legacy override idéntico | user_tb_permissions sigue funcionando como reemplazo |
| 2 | DENY bloquea ALLOW de rol | `addDenyResourcePermissions` + role allow → false |
| 3 | DENY bloquea write_all | `read_all`/`write_all` no ignoran DENY |
| 4 | DENY específico no afecta otros | DENY `delete` no bloquea `show`/`list` |
| 5 | Regresión zero | Tests existentes (23) pasan sin cambios |
| 6 | Sin DENY = comportamiento actual | Mismo resultado que antes |

---

## Fase 9: Documentación

### Actualizar

- `docs/ACL.md` — nueva sección "Deny Semantics"
- `docs/to-do/acl-improvement.md` — marcar como en progreso/completado
- `docs/CHANGELOG-acl.md` — registro del cambio

---

## Matriz de Compatibilidad

| Elemento | Cambio | Breaking |
|----------|--------|----------|
| `Acl::hasPermission()` | Interno: evaluate() | No |
| `Acl::can()` | Interno: evaluate() | No |
| `Acl::addRole()` | Sin cambios | No |
| `AclContext` | Nuevo campo `userDenyPerms` (default `[]`) | No |
| `AclSnapshot` | Nuevo campo `denyRolePerms` (default `[]`) | No |
| `config/acl.php` | Sin cambios necesarios | No |
| `user_tb_permissions` | Sigue funcionando como reemplazo | No |
| Tests existentes (23) | Sin cambios esperados | No |
| `BasicACL` / `FineGrainedACL` | Constructores sin cambios | No |

---

## Riesgos y Mitigaciones

| Riesgo | Mitigación |
|--------|-----------|
| Legacy override cambia semántica | `LegacyRuleAdapter` genera DENY para acciones no listadas = mismo efecto |
| Serialización cambia | Nuevas propiedades con defaults; `make acl --force` regenera |
| Performance extra | Check de DENY es O(1) — array key lookup |
| Deny rules sin trazabilidad | Campo `source` en PolicyRule |

---

## Progreso

- [ ] Fase 1: Data Model (RuleEffect, PolicyRule, LegacyRuleAdapter)
- [ ] Fase 2: AclSnapshot + AclContext nuevos campos
- [ ] Fase 3: AclEngine inner evaluate()
- [ ] Fase 4: Builder deny methods
- [ ] Fase 5: Contratos opt-in
- [ ] Fase 6: DB + API endpoints
- [ ] Fase 7: FineGrainedACL deny from DB
- [ ] Fase 8: Tests
- [ ] Fase 9: Documentación
