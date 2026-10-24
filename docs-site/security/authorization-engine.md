---
title: Motor de autorización y utilidades ACL
source:
  - docs/security/authorization-engine.md
source_revision: 5192298b785bdcdd8751d38eb6b7be97abb83d4d
status: partial
---

# Motor de autorización y utilidades ACL

La fachada ACL se describe en [ACL y autorización API](/security/acl). Las clases bajo `Core\Security` ofrecen snapshots, contextos, engine, policies, jerarquía y formatos de permisos. Son primitivas de autorización; su existencia no instala un guard HTTP ni hace que todos los controladores las usen.

## Snapshot, contexto y engine

`AclSnapshot` es un valor inmutable con roles, padres, permisos especiales válidos, allows/denies compilados por rol y datos explicativos por rol. `AclContext` es inmutable e incluye identidad/roles y mapas opcionales de permisos y denies del usuario.

`AclEngine` evalúa un snapshot y un contexto explícitos. `AuthorizationServiceInterface` define consultas de acciones y permisos, pertenencia a roles, comparación de conjuntos, evaluación de policies y denies explícitos. `AclEngineInterface` extiende ese contrato por compatibilidad; para nuevos tipos se puede usar `AuthorizationServiceInterface`.

La fachada `Acl` expone `getSnapshot()` y `getEngine()`. Construir esos objetos desde las declaraciones en memoria no consulta la base de datos ni confirma que un proveedor ACL esté activo. Métodos como `satisfiesPolicy()`, `hasAllPermissions()` y `hasAnyPermission()` construyen un contexto desde la identidad actual.

## Policies y comparación de roles

`AuthorizationPolicyInterface::isSatisfiedBy()` recibe `AclContext` y `AclEngineInterface`; permite expresar el permiso requerido sin fijar nombres de roles. `Acl::satisfiesPolicy()` evalúa la policy con un contexto construido desde el auth actual.

`roleDominates(candidate, target)` pregunta si el conjunto de permisos efectivos del candidato contiene todos los permisos del objetivo. No usa el linaje de roles. `RoleHierarchyService` proporciona operaciones de linaje: `isHigherRole()` devuelve `true`, `false` o `null` para jerarquía superior, no superior o roles no relacionados. Son comparaciones distintas.

El builder de contexto actual pasa UID nulo a los hooks de deny. El hook de FineGrainedACL devuelve entonces cero filas de usuario; los helpers de fachada que usan ese contexto no demuestran enforcement de denies por usuario. `hasResourcePermission()` también crea contexto sin esos denies.

## Capacidades y bits de tabla

`CapabilityTypeResolver` clasifica permisos como acción de recurso, especial de sistema, especial de dominio o desconocidos. Un permiso de dominio se reconoce si aparece en la lista DB recibida o contiene un punto. También enumera recursos conocidos y ofrece una sugerencia por distancia Levenshtein de hasta tres caracteres. Los comandos `acl can` y `acl explain` usan este resolver; consulta el [catálogo CLI](/cli/commands), que no se ejecutó en esta auditoría.

`TbPermissionBits` asigna estos valores:

| Acción | Bit |
| --- | ---: |
| `list_all` | 64 |
| `show_all` | 32 |
| `list` | 16 |
| `show` | 8 |
| `create` | 4 |
| `update` | 2 |
| `delete` | 1 |

`bitFor()` devuelve cero para una acción desconocida. `unpack()` produce un booleano por acción; `unpackGranted()` devuelve sólo las acciones activas.

`PermissionExplanation` es un objeto de valor para fuente, modo, resultado y reemplazo. El compilador añade explicaciones de rol al snapshot; no constituye por sí solo una traza de autorización de un usuario arbitrario.

## Alcance de verificación

Se inspeccionaron el engine, el compilador y los tests de jerarquía, pero no se ejecutaron en esta fase. No se consultó una identidad viva, una policy ni una autorización HTTP.
