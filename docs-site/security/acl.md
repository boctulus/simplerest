---
title: ACL y autorización de API
source:
  - docs/security/acl.md
  - docs/audit/authentication-acl.md
source_revision: 5192298b785bdcdd8751d38eb6b7be97abb83d4d
status: partial
---

# ACL y autorización de API

Esta referencia cubre el ACL core y su uso por los controladores de recursos API incorporados. La aplicación selecciona la implementación concreta y sus reglas. La [autenticación](/security/authentication) establece identidad; tener identidad no concede por sí solo permiso para una acción.

## De dónde salen las reglas

`Factory::acl()` incluye `config/acl.php`, pero no escoge por sí misma una implementación concreta. Ese archivo debe devolver una implementación del contrato ACL; la clase base `Core\\Security\\Acl` es abstracta.

El builder core admite roles por nombre e ID, roles guest y registered, herencia, permisos de recurso, permisos especiales y denegaciones explícitas por rol. `addInherit()` registra el padre y copia los arrays de permisos que el padre tiene en ese momento; el servicio de jerarquía también usa el linaje registrado para comparar roles.

Las fuentes de permiso siguen rutas de composición distintas; no hay una precedencia total única para todas. En la compilación descrita por la fuente, se combinan grants de rol, se suman permisos especiales del usuario, una máscara de tabla por usuario reemplaza el grant de rol para ese recurso y luego se aplican denies explícitos. Un deny explícito prevalece para el permiso coincidente; un grant especial por usuario se suma al del rol.

Los permisos de recurso son acciones por tabla: `show`, `show_all`, `list`, `list_all`, `create`, `update` y `delete`. `read` se expande a `show` y `list`; `write`, a `create`, `update` y `delete`. Capacidades especiales incluyen `read_all`, `write_all`, `grant`, `impersonate`, `fill_all` y `transfer`; una subclase ACL puede definir el catálogo permitido.

## Configuración de esta aplicación

El `config/acl.php` de este checkout selecciona el paquete opcional `FineGrainedACL`, obtiene nombres de roles y permisos especiales desde la base de datos, declara roles de aplicación y escribe un objeto ACL serializado. La variable local `$acl_cache` vale `false`, por lo que reconstruye y escribe el objeto en vez de restaurarlo desde el archivo. `BasicACL` y `FineGrainedACL` son integraciones opcionales; el factory core no las descubre ni enlaza automáticamente.

El controlador JWT del paquete opcional resuelve roles desde un campo de rol del usuario cuando está presente, o desde `user_roles` y `roles`. Login y API key cargan `user_tb_permissions` y `user_sp_permissions`; el JWT lleva esos permisos en su payload de acceso. `FineGrainedACL` sobrescribe un hook para cargar filas de denegación por usuario, pero eso no demuestra que todos los helpers ACL las consulten. Los contextos construidos por la fachada y la llamada por defecto a `hasSpecialPermission()` pasan UID nulo; `hasResourcePermission()` tampoco agrega denies de usuario. Consulta [Implementaciones ACL](/security/acl-implementations) y el [motor de autorización](/security/authorization-engine).

La configuración actual declara `read_all` y `write_all` para guest. Son grants explícitos de esta aplicación; el ACL core no los concede por defecto. El catálogo efectivo de permisos especiales y el resultado de ejecución de la base de datos no se consultaron.

## Compuerta de recursos API

El camino incorporado sigue este orden:

1. `ResourceController` ejecuta `auth()->check()` durante la construcción.
2. `ApiController` resuelve la tabla del recurso y consulta permisos para la identidad actual.
3. Agrega las acciones permitidas a la lista de métodos invocables.
4. `FrontController` ya construyó el controlador y luego comprueba que el método resuelto esté en esa lista; si no, responde 403 antes de invocar el método de acción.

La comprobación ocurre después del constructor. Aplica al despacho API versionado que no sea de autenticación. Las acciones auth omiten esa compuerta y los controladores web normales no tienen un guard ACL global. Un controlador API personalizado fuera de la jerarquía `ResourceController`/`ApiController` debe implementar sus propias comprobaciones de credenciales y autorización; el gate de `FrontController` compara nombres de métodos.

En el camino común, GET se habilita con `read_all` o acciones de lectura del recurso; POST con `write_all` o `create`; PUT/PATCH con `write_all` o `update`; DELETE con `write_all` o `delete`. `get` sirve tanto para listar como para recuperar un elemento. La guía de [endpoints automáticos](/api/automatic-endpoints) detalla la resolución y los parámetros.

La ausencia de credencial asigna el rol guest, no produce automáticamente 401. Si ACL no habilita la acción para guest, la compuerta responde 403. Credenciales inválidas se rechazan antes con 401. El resultado de los grants de guest configurados aquí depende también de la integración ACL y su catálogo de base de datos; no se confirmó mediante un request integrado.

La fuente también observa que el `ApiController` incorporado llama incondicionalmente a dos clases de impersonación que no están definidas ni son cargables mediante Composer en este repositorio. Los tests aislados usan tipos de prueba, así que no demuestran que esta ruta API arranque en una aplicación real con la configuración disponible.

## Wildcards y versiones

El engine deniega permisos no declarados; roles desconocidos no agregan autorizaciones. `read_all` y `write_all` son capacidades globales explícitas que el compilador expande a acciones de lectura o escritura sobre recursos.

Un grant literal de recurso `*` se rechaza en el builder, la compilación de roles, la compilación por usuario y las máscaras de tabla por usuario. El bucket interno `allow['*']` se reserva para expansión de las capacidades globales. Los denies wildcard explícitos sí están soportados y se revisan en los caminos de lookup.

`ApiHandler` valida y conserva la versión de ruta, pero resuelve el controlador por slug de recurso. `ApiController` indexa permisos por nombre de tabla y no usa una clave de versión. El código de un controlador de aplicación puede ramificarse por versión, pero eso no vuelve versionado al ACL core.

## Máscaras de usuario, método HTTP y ownership

`ApiController` elige los callables ACL a partir de `Request::method()`, el mismo método efectivo que usa `ApiHandler`; el request conserva el valor tras consumir query parameters. Así, los overrides HTTP por header o por `_method` usan la misma acción en ambas rutas.

Una máscara de permisos de tabla por usuario no nula controla los callables del recurso, los flags de lectura GET y los bypasses de ownership `list_all`/`show_all`; reemplaza los grants de recurso derivados del rol. El bit create corresponde a `post`; el bit update a `put` o `patch`. Las capacidades globales `read_all` y `write_all` siguen siendo efectivas. La regresión focalizada pasó 9 tests y 76 assertions con doubles; no cargó permisos de base de datos ni comprobó el SQL.

El camino default de ownership de `ApiController` cubre recursos `belongs_to`; las carpetas usan otra ruta con comprobación ACL. Esto no establece reglas genéricas ACL por fila o por atributo. Esas afirmaciones de guías históricas no forman parte del contrato actual del framework.

## Evidencia y límites

Las suites focalizadas citadas para el engine ACL, denies, permisos compilados, wildcard y dispatch sumaron 52 tests y 112 assertions; los tests de callable usaron doubles y clases de impersonación de prueba. Tests de jerarquía se inspeccionaron pero no se incluyeron en esa ejecución. Un probe local con controller sintético y callable vacío observó 403; no fue una denegación ACL de recurso respaldada por base de datos.

No se verificó la política efectiva cargada desde tablas ACL, un JWT/API key válidos, ni una autorización automática end-to-end con base de datos. Los resultados dependen de roles, permisos, configuración y paquetes de la aplicación. El ledger canónico conserva los claims históricos clasificados y los detalles de pruebas.
