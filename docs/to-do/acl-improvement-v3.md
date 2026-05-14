# ACL Improvement v3 — Compiled Effective Permissions Evolution

## Objetivo

Evolucionar el ACL hacia un modelo de:

* permisos efectivos compilados
* resolución determinística
* evaluación O(1)
* bajo costo cognitivo
* frontend administrativo explicable

sin romper:

* API pública existente
* compatibilidad backward
* snapshots serializados
* semántica histórica del framework

---

# Filosofía del Sistema

SimpleRest ACL es un:

```text id="1m6c6q"
Business ACL Framework
```

NO un:

```text id="df4v4q"
General-purpose IAM / Zero-Trust Policy Engine
```

Por lo tanto el sistema prioriza:

| Prioridad | Objetivo                                 |
| --------- | ---------------------------------------- |
| ✅         | Determinismo                             |
| ✅         | Explicabilidad humana                    |
| ✅         | FrontEnd administrativo simple           |
| ✅         | Performance O(1)                         |
| ✅         | Effective permissions                    |
| ✅         | Backward compatibility                   |
| ❌         | Runtime policy graph resolution          |
| ❌         | Enterprise deny precedence semantics     |
| ❌         | Dynamic multi-policy conflict evaluation |

---

# Principio Arquitectónico Central

La autorización se resuelve principalmente en:

```text id="k3f1rm"
build-time / compilation-time
```

NO en runtime.

---

# Modelo Conceptual

## El sistema produce:

```text id="nuxvjlwm"
effective permissions
```

y NO:

```text id="v9hy4e"
runtime policy conflict graphs
```

---

# Resolución Conceptual

```text id="frjlwm0"
Roles
    ↓
Inheritance expansion
    ↓
Decorators / user overrides
    ↓
Special permissions
    ↓
Effective permission compilation
    ↓
Serialized snapshot / token payload
    ↓
O(1) runtime evaluation
```

---

# Semántica Oficial

## El framework mantiene:

```text id="jlwm11"
replacement semantics
```

para overrides administrativos.

---

# Importante

El sistema NO adopta:

```text id="jlwm12"
DENY > ALLOW global semantics
```

como modelo principal de autorización.

---

# Razón

El objetivo es mantener:

* UI determinística
* evaluación trivial
* explicabilidad simple
* bajo costo cognitivo
* permisos efectivos fácilmente visualizables

---

# Conceptos Fundamentales

## 1. Effective Permissions

Todo usuario posee un conjunto final de permisos efectivos ya resueltos.

Ejemplo:

```php
[
    'products' => [
        'show'   => true,
        'list'   => true,
        'create' => false,
        'update' => false,
        'delete' => false,
    ]
]
```

La resolución de:

* herencia
* decorators
* overrides
* special permissions

ocurre ANTES del runtime.

---

# 2. Replacement Semantics

Los overrides administrativos:

```text id="jlwm13"
REEMPLAZAN
```

el set de permisos sobre un recurso.

NO generan conflictos dinámicos ALLOW vs DENY.

---

# Ejemplo

```php
user_tb_permissions:
products => ['show', 'list']
```

significa:

```text id="jlwm14"
El set efectivo para products pasa a ser:
show=true
list=true
todo lo demás=false
```

NO:

```text id="jlwm15"
DENY explícito para cada acción ausente
```

---

# 3. Runtime Evaluation

Runtime debe ser:

```text id="jlwm16"
O(1)
```

sin:

* recorrer ancestros
* resolver conflictos
* fusionar políticas
* evaluar DAGs

---

# Algoritmo Runtime

```php
if (isset($effectiveDenies[$resource][$action])) {
    return false;
}

return isset($effectiveAllows[$resource][$action]);
```

---

# Importante

`effectiveDenies` NO implica deny semantics públicas.

Es un:

```text id="jlwm17"
compiled negative cache
```

interno de optimización.

---

# Arquitectura

```text
Builder Layer
    Acl.php
        ↓
Compilation Layer
    EffectivePermissionCompiler
        ↓
Immutable Snapshot
    AclSnapshot
        ↓
Runtime Engine
    AclEngine
        ↓
O(1) Permission Lookup
```

---

# Fase 1 — Effective Permission Compiler

## Nuevo namespace

```text
src/framework/Security/Compiler/
```

---

# Nuevo archivo

```text
EffectivePermissionCompiler.php
```

---

# Responsabilidad

Resolver completamente:

* role inheritance
* special permissions
* decorators
* user overrides
* replacement semantics

y producir:

```php
[
    'effectiveAllows' => [],
    'effectiveDenies' => [],
    'explanations'    => []
]
```

---

# Output esperado

```php
[
    'products' => [
        'show'   => true,
        'list'   => true,
    ]
]
```

---

# Deny cache interno

```php
[
    'products' => [
        'delete' => true,
        'update' => true,
    ]
]
```

---

# Nota importante

Los denies internos:

* NO son parte del modelo público
* NO representan policy conflicts
* NO tienen precedencia global
* NO son reglas IAM-style

Son solamente:

```text id="jlwm18"
compiled optimization shortcuts
```

---

# Fase 2 — AclSnapshot Evolution

## Agregar

```php
public readonly array $effectiveAllows = [];
public readonly array $effectiveDenies = [];
public readonly array $permissionExplanations = [];
```

---

# Compatibilidad

Todos los nuevos campos deben tener defaults.

No romper serialización previa.

---

# Fase 3 — AclContext Evolution

## Agregar

```php
public readonly ?array $compiledPermissions = null;
```

---

# Objetivo

Permitir:

* JWT precompiled permissions
* request-level cache
* multi-tenant overrides
* future optimization

---

# Fase 4 — Runtime Engine Simplification

## Nuevo comportamiento

`AclEngine::can()` deja de resolver jerarquías dinámicamente.

Sólo consulta:

```php
$context->compiledPermissions
```

o:

```php
$snapshot->effectiveAllows
```

---

# Runtime esperado

```php
public function can(
    AclContext $context,
    string $action,
    string $resource
): bool {

    if (isset($context->compiledPermissions['deny'][$resource][$action])) {
        return false;
    }

    return isset(
        $context->compiledPermissions['allow'][$resource][$action]
    );
}
```

---

# Fase 5 — Explanation Layer

## Nuevo archivo

```text
src/framework/Security/Explanation/PermissionExplanation.php
```

---

# Objetivo

Permitir frontend administrativo determinístico.

---

# Ejemplo

```php
[
    'products.delete' => [
        'granted' => false,
        'source'  => 'user_tb_permissions',
        'mode'    => 'replacement',
        'replaced_role_permissions' => true,
    ]
]
```

---

# FrontEnd Administrativo

El frontend SIEMPRE trabaja sobre:

```text id="jlwm19"
effective permissions
```

NO sobre:

```text id="jlwm20"
raw policy graphs
```

---

# Objetivo UX

El checkbox debe representar:

```text id="jlwm21"
la verdad final
```

sin ambigüedad.

---

# Ejemplo correcto

```text
[X] show
[X] list
[ ] create
[ ] update
[ ] delete
```

---

# El frontend NO debe necesitar resolver

* herencia
* precedencias
* policy conflicts
* explicit deny precedence
* specificity wars

---

# Fase 6 — Security Constraints Layer

## Nuevo concepto

Separar:

```text id="jlwm22"
Business Authorization
```

de:

```text id="jlwm23"
Hard Security Constraints
```

---

# Hard Constraints

SÍ usan:

```text id="’wini24"
DENY > EVERYTHING
```

---

# Casos válidos

* suspended accounts
* immutable records
* legal hold
* tenant isolation
* ownership freeze
* embargo
* compliance locks

---

# Importante

Estas restricciones:

* NO pertenecen al ACL administrativo principal
* NO participan en replacement semantics
* NO aparecen como permisos administrativos normales

---

# Arquitectura

```text
Business ACL
    ↓
compiled effective permissions
    ↓
Hard Security Constraints
    ↓
final authorization
```

---

# Runtime final

```php
if ($securityConstraintDeny) {
    return false;
}

return $businessAclDecision;
```

---

# Fase 7 — Role Hierarchy Clarification

## Mantener

```php
hasRoleOrHigher()
isHigherRole()
```

---

# Pero documentar explícitamente

Estos métodos son:

```text id="’wini25"
heuristics / utilities
```

NO autorización definitiva.

---

# La autorización real vive en:

```php
AclEngine::can()
```

---

# Fase 8 — Performance Optimization

## Objetivo

Toda evaluación runtime:

```text id="’wini26"
O(1)
```

---

# Reglas

## PROHIBIDO en runtime

* recorrer ancestros
* mergear arrays
* in_array()
* resolver decorators
* resolver replacement semantics
* expandir herencia

---

# Todo eso debe ocurrir en:

```text id="’wini27"
compile/build phase
```

---

# Estructuras internas

## Usar hash maps

NO:

```php
['read', 'write']
```

---

# Usar:

```php
[
    'read'  => true,
    'write' => true
]
```

---

# Fase 9 — Backward Compatibility

## Garantías

| Elemento               | Compatibilidad |
| ---------------------- | -------------- |
| `Acl::hasPermission()` | ✅              |
| `Acl::can()`           | ✅              |
| `user_tb_permissions`  | ✅              |
| `addRole()`            | ✅              |
| `addInherit()`         | ✅              |
| `BasicACL`             | ✅              |
| `FineGrainedACL`       | ✅              |
| snapshots serializados | ✅              |
| ACL existing configs   | ✅              |

---

# Fase 10 — Tests

## Nuevos tests

```text
tests/unit-tests/AclCompiledPermissionsTest.php
```

---

# Verificaciones

| Test                   | Objetivo                         |
| ---------------------- | -------------------------------- |
| inheritance flattening | permisos heredados compilados    |
| replacement semantics  | override reemplaza correctamente |
| runtime O(1)           | no traversal                     |
| effective deny cache   | shortcut correcto                |
| explanation generation | frontend determinístico          |
| backward compatibility | tests existentes pasan           |

---

# No Objetivos

El framework NO busca implementar:

* AWS IAM semantics
* Cedar-like policy language
* OPA/Rego engine
* runtime policy graphs
* multi-policy precedence trees
* distributed policy resolution
* dynamic specificity wars

---

# Resultado Esperado

## El sistema final será:

| Característica            | Resultado                |
| ------------------------- | ------------------------ |
| Runtime authorization     | O(1)                     |
| FrontEnd administrativo   | Determinístico           |
| Effective permissions     | Sí                       |
| Explainability            | Alta                     |
| Cognitive load            | Bajo                     |
| Backward compatibility    | Completa                 |
| Security constraints      | Separadas                |
| Runtime policy conflicts  | No                       |
| IAM-style deny precedence | Sólo en hard constraints |

---

# Conclusión Arquitectónica

SimpleRest ACL evoluciona hacia un:

```text id="’wini28"
Compiled Effective Permission System
```

manteniendo:

* semántica histórica
* replacement semantics
* business-oriented authorization
* frontend administrativo simple
* runtime extremadamente rápido

sin transformarse en un:

```text id="’wini29"
general-purpose enterprise IAM engine
```
