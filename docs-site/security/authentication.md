---
title: Autenticación
source:
  - docs/security/authentication.md
  - docs/audit/authentication-acl.md
source_revision: 67b105dbb771e4ca5826aa43578822207976243e
status: partial
---

# Autenticación

Esta referencia describe el camino actual del framework. El resultado también depende del controlador de aplicación, del esquema de usuarios, de los handlers configurados y de los secretos. La auditoría canónica conserva las afirmaciones históricas y sus evidencias.

## Resolución de rutas y comprobación

La configuración predeterminada de `front_behaviors` registra handlers para parsear el request, resolver rutas API y de autenticación, producir la salida, ejecutar middleware y gestionar errores. `RequestHandler` identifica la ruta de autenticación con versión; `AuthHandler` elige una acción en la clase de aplicación `MyAuthController`. `AuthHandler` resuelve rutas, pero no valida credenciales.

La clase base de recursos API es `ResourceController`. Su constructor llama a `auth()->check()` antes de que el constructor de `ApiController` prepare los callables ACL. Los controladores de aplicación ordinarios no heredan automáticamente esa comprobación. En el orden predeterminado, `FrontController` ejecuta la acción antes del handler de middleware; ese middleware no es una barrera de autenticación anterior a la acción.

Con `remove_api_slug = false`, la ruta de autenticación API tiene la forma `/api/{version}/auth/{action}`. Si se activa, desaparece el segmento `api`, pero se conserva la versión. Las rutas web de `config/routes/auth.php`, como `/auth/login` y `/auth/rememberme`, son páginas GET separadas de las acciones de autenticación API.

## Detección y validación de credenciales

| Credencial | Detección | Validación e identidad |
| --- | --- | --- |
| JWT bearer | Header `Authorization` o parámetro `token`, que se transforma a valor `Bearer` | `AuthController::check()` decodifica con clave y algoritmo configurados y comprueba claims requeridos, expiración y restricciones habilitadas de IP o user-agent. |
| API key | Header `X-API-KEY` o parámetro `api_key` | `AuthController::check()` busca la clave en `api_keys`, obtiene el ID de usuario y carga su rol y permisos. |

Si llegan ambos tipos, `Request::authMethod()` elige primero API key. `Request::isAuthenticated()` solo indica que se detectó alguna credencial en un request no CLI; no certifica su validez. Para identidad validada se usa el camino `IAuth::check()`.

Si no hay credencial, `AuthController::check()` asigna el rol guest configurado, un ID de usuario nulo y permisos específicos de usuario vacíos. Esa identidad no autenticada no implica una denegación inmediata: el resultado depende de las reglas ACL para guest.

## Usuario, roles y permisos

`auth()` devuelve el proveedor `IAuth`. En este repositorio, `Factory::auth()` construye la clase de aplicación `MyAuthController`; `ResourceController` también permite inyectar una implementación `IAuth`. La interfaz expone `check()`, `uid()`, `getRoles()`, `getPermissions()`, `isGuest()` e `isRegistered()`.

En un login con contraseña válido, el controlador core consulta la tabla configurada y los nombres de campo declarados por el modelo de usuario. Verifica el hash, comprueba el estado activo si existe esa columna, resuelve roles y carga permisos de tabla y permisos especiales del usuario. El payload del access token lleva ID, roles, permisos, estado activo y lista de acceso a bases de datos. El refresh token se firma por separado y contiene el ID de usuario, no una copia de roles y permisos.

Para API keys, roles y permisos se cargan desde la base después de asociar la clave con un usuario. Para JWT, el payload proporciona roles y permisos; si existe el campo de rol configurado en la tabla de usuarios, el ID se mapea mediante ACL. Si una cuenta registrada no trae roles, se aplica el rol registered configurado. `uid()`, `getRoles()` y `getPermissions()` exponen el estado del request ya comprobado. Los ejemplos antiguos `getCurrentUser()` y `getCurrentUserId()` no pertenecen a la interfaz `IAuth` actual ni al controlador core.

## Login, renovación y códigos

`AuthController::login()` emite access y refresh JWT después de validar las credenciales. La renovación es una acción separada `token()` que acepta un refresh token; la comprobación del access token rechaza un token inválido o vencido, sin renovarlo silenciosamente.

Las rutas core usan HTTP 401 para credenciales de login incorrectas, API keys inválidas y JWT inválidos o vencidos. Las cuentas deshabilitadas o pendientes usan 403 en las ramas pertinentes. La compuerta de callables del API también usa 403 cuando el método no está permitido.

En probes HTTP locales se observó 400 para una cabecera `Authorization: Basic`, 401 para una muestra de JWT malformado y 403 para un controlador sintético con lista de callables vacía. Son resultados de esas rutas y muestras; no fijan la respuesta de todos los tokens malformados ni una denegación ACL con base de datos. Tras el ajuste de SAPI del 2026-09-24, las respuestas de aplicación verificadas usaron líneas HTTP/1.1 válidas.

## Configuración y dependencias de aplicación

`config/config.php` define la tabla de usuarios y lee desde variables de entorno los secretos de access token, refresh token y email token. En la configuración fuente consultada, los vencimientos son 3.600 segundos para access, 1.209.600 para refresh y 3.600 para email. `AuthController::gen_jwt()` usa el valor configurado salvo que quien llama proporcione una duración explícita. Son valores de configuración fuente: no se verificó la emisión de tokens con credenciales válidas.

El flujo `pwdv` de restablecimiento de contraseña que incluye esta aplicación está implementado en `app/Controllers/MyAuthController.php`, como override de la aplicación; no forma parte del contrato genérico de `AuthController`. La presencia de controladores o configuración Google/Facebook en la aplicación no acredita integración OAuth del core.

Una aplicación independiente debe aportar una clase compatible `MyAuthController` y el wiring que seleccionan `AuthHandler` y `Factory`. La configuración de este checkout no es un default universal del framework. La guía de [ACL](/security/acl) detalla cómo se autorizan las acciones después de resolver la identidad.

## Evidencia y límites

Las fuentes de tests incluyen casos HTTP de autenticación y tests de la sobrescritura de restablecimiento de contraseña de esta aplicación; se inspeccionaron, pero no se ejecutaron en esta fase. No se completó un login válido, un request con API key o JWT válidos ni una autorización respaldada por la base de datos. Las comprobaciones descritas distinguen trazado de fuente de reproducción funcional.
