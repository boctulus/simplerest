---
title: Implementaciones ACL
source:
  - docs/security/acl-implementations.md
source_revision: 5192298b785bdcdd8751d38eb6b7be97abb83d4d
status: partial
---

# Implementaciones ACL

El builder y evaluador core están en [ACL y autorización API](/security/acl). Aquí se distinguen la base abstracta y los paquetes ACL opcionales encontrados en el checkout. El core no selecciona un paquete automáticamente: la aplicación elige la implementación en `config/acl.php`.

## Comportamiento de los paquetes

| Paquete | Comportamiento trazado | Límite |
| --- | --- | --- |
| `boctulus/simplerest-basic-acl` | La subclase `Boctulus\BasicACL\Acl` carga `roles` y guarda IDs/nombres antes de usar el builder core. | No añade un catálogo de permisos especiales ni un loader DB de denies de usuario. |
| `boctulus/simplerest-fine-grained-acl` | La subclase lee `sp_permissions` y `roles` en la conexión default, ofrece lecturas frescas de permisos y sobrescribe el hook de denies por recurso. Persiste roles nuevos salvo que el catálogo esté diferido. | Si `sp_permissions` está vacío, setup lanza una excepción. El hook de user-deny devuelve filas sólo con UID no nulo; no todos los helpers core pasan ese UID. |

La reconciliación diferida de FineGrainedACL puede escribir roles faltantes dentro de una transacción, después de validar nombres, IDs y asignaciones existentes. Esa ruta modifica la base de datos y no se ejecutó.

Los manifests de ambos paquetes los marcan como propietarios y declaran dependencia de SimpleRest 2.x/3.x. Los providers que aparecen en metadata tienen métodos `register()` y `boot()` vacíos. La configuración de este checkout selecciona FineGrainedACL explícitamente; no es una política universal del framework.

La implementación FineGrained no demuestra que los denies de usuario se apliquen en cada helper. Los contextos que construye la fachada core y la llamada por defecto a `hasSpecialPermission()` pasan UID nulo; `hasResourcePermission()` construye un contexto sin user-deny rows. Revisa la ruta de llamada antes de confiar en esa cobertura.

## Alcance de verificación

Se inspeccionaron código y manifests, pero no se instaló ningún paquete ni se consultó la base de datos. No se ejecutó reconciliación de roles o una petición de autorización.
