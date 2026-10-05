---
title: Seguridad
source:
  - docs/security/authentication.md
  - docs/security/acl.md
source_revision: 1dff834829126b0c1c5aa60bd3ff13a75e7859f4
status: partial
---

# Seguridad

Esta guía describe mecanismos del framework trazados en fuente y los distingue de la configuración de cada aplicación. No se realizó una autenticación válida ni una autorización automática respaldada por base de datos.

## Autenticación: detección y validación

El framework detecta JWT bearer desde `Authorization` o desde el parámetro `token`, y API key desde `X-API-KEY` o `api_key`. Si ambos están presentes, `Request::authMethod()` elige API key. Detectar un valor no lo valida: `Request::isAuthenticated()` indica presencia de credencial en una petición no CLI; la validación y la identidad corresponden a `IAuth::check()`.

`AuthHandler` resuelve acciones de autenticación; no valida credenciales por sí mismo. En el camino incorporado, `ResourceController` llama a `auth()->check()` antes de que `ApiController` prepare sus acciones ACL. Ese control no se aplica automáticamente a controladores de aplicación que no hereden de esa ruta.

El código rechaza credenciales inválidas o vencidas en la ruta de autenticación y usa 401 para esas comprobaciones; cuentas deshabilitadas o pendientes reciben 403 en las rutas pertinentes. No se debe generalizar un status a todos los controladores de una aplicación.

## Autorización con ACL

`Factory::acl()` carga `config/acl.php`, que debe seleccionar una implementación del contrato ACL. La clase core `Acl` es abstracta; la política activa y sus datos pertenecen a la aplicación.

Las acciones de recurso incluyen `show`, `show_all`, `list`, `list_all`, `create`, `update` y `delete`. El builder expande `read` a `show` y `list`, y `write` a `create`, `update` y `delete`. `read_all` y `write_all` son capacidades explícitas de alcance global; los permisos no listados se deniegan por defecto y roles desconocidos no añaden autorizaciones.

Los grants de roles, permisos especiales por usuario, máscaras de tabla por usuario y denies explícitos se compilan por rutas distintas. Una máscara de tabla reemplaza el grant de rol para ese recurso; los grants especiales por usuario se suman a los del rol, y un deny explícito prevalece para el permiso coincidente. No existe una prioridad universal única para todas esas fuentes.

En la configuración actual de este repositorio, `config/acl.php` declara `read_all` y `write_all` para el rol `guest`. Es una decisión de esta aplicación, no un grant predeterminado del ACL core. La política efectiva de base de datos no se consultó en esta revisión.

En el despacho API incorporado, el controlador construye su lista de acciones según los permisos y `FrontController` comprueba el método antes de invocarlo. Las acciones de autenticación omiten esa compuerta; los controladores web ordinarios no tienen un control ACL global. Un controlador API personalizado fuera de la jerarquía incorporada debe definir su propia autenticación y autorización.

## Límites de verificación

Las pruebas focalizadas de ACL y despacho usaron doubles para varios casos y no cargaron permisos desde una base de datos ni comprobaron el SQL resultante. Tampoco se verificó un request con JWT válido, API key válida o una denegación de recurso integrada de extremo a extremo. Los resultados efectivos dependen de `config/acl.php`, roles, permisos y handlers de la aplicación.
