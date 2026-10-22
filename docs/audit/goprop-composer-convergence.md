# Convergencia del core SimpleRest–GoProp

**Estado:** Fase 1 completada; iniciando Fase 2. La migración de GoProp no ha comenzado.
**Corte de código:** 2026-10-22.
**Auditoría de partida:** [integración Composer de SimpleRest en GoProp](goprop-composer-integration-2026-10.md).

Este documento es el inventario vivo de decisiones y cambios. No sustituye la auditoría de partida ni presenta como resueltas las diferencias que siguen pendientes de lectura y prueba.

## Reglas de alcance confirmadas

- El paquete Composer del core ya existe: el composer.json raíz declara boctulus/simplerest. El core no debe duplicarse bajo otro nombre o registro.
- Ya existen boctulus/simplerest-pdf, boctulus/simplerest-code-tools, boctulus/basic-acl y boctulus/fine-grained-acl. En la fase de empaquetado se evaluará reducir o cambiar el alcance de estos paquetes y sus manifiestos antes de proponer identidades nuevas.
- En el checkout no se encontró un archivo independiente llamado packagelist. La fuente comprobable local son el manifiesto raíz y los composer.json existentes. La lista de publicaciones efectivas en Packagist sigue pendiente de verificar por referencia.
- composer.json raíz asigna el prefijo Boctulus\Simplerest\Core\Libs\ tanto a src/framework/Libs/ como a los directorios fuente locales de simplerest-pdf y simplerest-code-tools; sus manifiestos vuelven a declarar ese prefijo. Esta propiedad compartida deberá resolverse ajustando los límites existentes, no creando copias del core.
- El nombre simplerest-cli no existe entre los paquetes locales inspeccionados. No se crea ni publica como parte de esta fase.
- app/Commands/ sigue siendo propiedad de cada aplicación. El contrato de comando y su registro pertenecen al core; la distribución de implementaciones se decidirá usando los paquetes existentes y evidencia de consumidores.
- La modularización ya tiene una tarjeta vigente en [simplerest-composer-core-packages.md](../to-do/simplerest-composer-core-packages.md). Esta auditoría registra decisiones/evidencia del core y no duplica esa tarea.

## Rebaseline de Composer y distribución

- Packagist publica `boctulus/simplerest` `v1.0.3`; su metadata resuelve el commit `e686ceee139f69c444c45a3c4f941842440f8a4f`. El tag local `v1.0.3` apunta al commit `4d44fccf8261d69334f78ba81d2dcdf3cb28781a`; el tag remoto apunta a `6d86ff75e1aa0a312af853b0c776667cffc24d0a`. No se modificó ninguno.
- Un consumidor temporal limpio instaló `v1.0.3` con `--no-dev --optimize-autoloader`; Composer resolvió PHPMailer `7.1.1`. La carpeta instalada de SimpleRest contiene 9.366 archivos y 667.115.501 bytes (636,21 MiB), incluidos `app/`, `docs/`, `unit-tests/`, `packages/` y assets. `ReflectionClass` confirmó que `Model`, `DB` y `CommandRegistry` cargan desde `vendor/boctulus/simplerest/src/framework/`.
- En el mismo consumidor, `preferred-install=source` falló al intentar hacer checkout de `e686ceee…`: Git respondió `reference is not a tree`. Por ese fallo no hay una medida válida del árbol source instalado; la referencia no reproducible es el bloqueo.
- Las identidades locales `boctulus/simplerest-pdf`, `boctulus/simplerest-code-tools`, `boctulus/basic-acl` y `boctulus/fine-grained-acl` devolvieron 404 en Packagist. Sus cuatro manifiestos locales pasan `composer validate --strict`, pero aún no son paquetes instalables desde Packagist.
- `composer validate --strict` también pasó para el manifiesto raíz. No existe `.gitattributes` en el checkout; el ZIP de Packagist conserva el árbol de desarrollo completo. No se ejecutó `php com pack build`.
- `D:\laragon\www\simplerest-pack` tiene cambios locales y no tiene remoto Git configurado. No se leyó como fuente del paquete ni se usó como destino; permanece intacto.
- El manifiesto que Packagist resuelve exige PHPMailer `^7.1`; GoProp declara `^6.9`. El `composer.lock` del consumidor GoProp fija `6.12.0`. El desacuerdo sigue pendiente de corregir en Fase 2.

## Rebaseline de los árboles versionados

La comparación usa blobs Git del HEAD de cada repositorio, no hashes de archivos de trabajo que podrían diferir por finales de línea.

| Árbol | Ref corta | Archivos src/framework/ |
| --- | --- | ---: |
| SimpleRest | fa54c6a39 (master) | 272 |
| GoProp | 0fc4aa5f (main) | 274 |
| Rutas comunes | — | 262 |
| Comunes con blob distinto | — | 34 |
| Exclusivas de GoProp | — | 12 |
| Exclusivas de SimpleRest | — | 10 |

La comparación está calculada sobre los blobs de esos HEAD, sin incorporar archivos sin commit. En el corte, src/framework/ no tiene cambios locales en ninguno de los dos repositorios. SimpleRest conserva el cambio local preexistente en app/Views/templates/adminlte_tpl.php; no forma parte de estos commits. GoProp `main` estaba sincronizada con `origin/main` y conserva únicamente `automation/screenshots/` sin seguimiento; no se modificó ese repositorio. El nuevo HEAD de GoProp mantiene las mismas 34 diferencias y 22 rutas exclusivas registradas aquí.

## Cambios ya integrados en SimpleRest

| Rutas | Decisión | Evidencia y pruebas | Commit |
| --- | --- | --- | --- |
| src/framework/Traits/QueryBuilderTrait.php | A — Se incorporó el contrato de GoProp: orderBy($field, $direction) y el arreglo asociativo existente; se valida ASC/DESC y se rechazan argumentos ambiguos. | unit-tests/query-builder/QueryBuilderOrderByContractTest.php: 6/6 pruebas; php -l pasó. No se abrió conexión de base de datos. | 52df05ea1 |
| src/framework/Request.php | A — Se normalizan nombres de cabecera para Apache y PHP built-in, se leen CONTENT_TYPE/CONTENT_LENGTH y se acepta application/json con parámetros. Se conservaron las APIs inmutables que ya tenía SimpleRest. | unit-tests/http/RequestInputParsingTest.php: 3/3 pruebas; php -l pasó. | 1795e8925 |
| src/framework/Libs/Url.php, src/framework/Exceptions/InvalidRequestBodyException.php, index.php | A — El JSON malformado ahora produce una excepción de cliente tipada y el entrypoint responde HTTP 400 con un mensaje genérico. | La misma suite HTTP pasó. Una petición POST local con application/json; charset=utf-8 y JSON inválido devolvió HTTP 400 y el cuerpo INVALID_REQUEST_BODY; no llegó a ejecutar una ruta ni una operación de base de datos. | 1795e8925 |
| src/framework/Libs/Validator.php, src/framework/Api/ApiController.php | A — Los filtros GET ignoran límites de longitud y rango destinados a escrituras, pero siguen validando el tipo del valor. | unit-tests/validator/ValidatorSkipBoundsContractTest.php: 2/2 pruebas; php -l pasó. No se arrancó la aplicación ni se abrió una conexión. | e5f22ac49 |
| src/framework/Response.php | A — Al desenvolver data, se conservan los metadatos hermanos y se regeneran status_code/error. Se mantiene el emisor HTTP de SimpleRest y se excluye la auditoría de impersonación de GoProp. | unit-tests/http/ResponseEnvelopeMetadataTest.php: 3/3 pruebas, 10 aserciones; php -l pasó; sin bootstrap ni base de datos. | 820d4aa01 |
| src/framework/Libs/Files.php | A — Si realpath() no resuelve la ruta al generar un error de permisos, se conserva la ruta original en el mensaje. | unit-tests/files/FilesWritablePathTest.php: 1/1 prueba, 3 aserciones en WSL/PHP 8.1; no se creó ningún archivo. PHP 8.4 lintó ambos archivos y reportó una deprecación previa en getAbsolutePath(). | d15a4d009 |
| src/framework/Libs/LangDetector.php | A — Se agrega italiano y detección por escritura para hindi/bengalí, se evita dividir por cero y se incorpora detect(). Se excluyen indicadores de vocabulario de GoProp. | unit-tests/translator/LangDetectorTest.php: 4/4 pruebas y 9 aserciones en PHP 8.1 y 8.4. En PHP 8.4 aparecieron deprecaciones existentes en Strings.php. | 14f793048 |
| src/framework/Libs/Url.php | A — Se añade getAppUrl() para leer la URL pública configurada sin depender del host de una petición. | unit-tests/http/UrlAppUrlTest.php: 2/2 pruebas en PHP 8.1 y 8.4; sin bootstrap ni base de datos. | b2440269b |
| src/framework/Libs/CommandRegistry.php | A — Sin subcomando, los flags muestran ayuda del grupo y no despachan el primer archivo en orden alfabético. | unit-tests/command/CommandRegistryDispatchTest.php: 2/2 pruebas, 11 aserciones en PHP 8.1 y 8.4. | ce36c0ca0 |
| src/framework/Libs/CorsHandler.php | A — Solo usa HTTP_ORIGIN para CORS. Sin Origin, no bloquea la petición ni agrega cabeceras CORS; un Origin explícitamente no permitido sigue rechazado. | unit-tests/http/CorsHandlerOriginTest.php: 2/2 pruebas, 4 aserciones en PHP 8.1 y 8.4. | 325851c27 |
| src/framework/FrontController.php | A — Distingue invitado sin permiso (401) de usuario autenticado sin permiso ACL (403). No copia el 401 incondicional de GoProp, que no distingue ambos estados. | FrontControllerAuthorizationStatusTest.php y ApiAuthorizationDispatchTest.php: 11/11 pruebas, 78 aserciones en PHP 8.1 y 8.4; sin base de datos. | e8895db7e |
| src/framework/Handlers/ErrorHandler.php | A / B — Se porta la validación del código de excepción como status HTTP (solo 100–599, otros valores producen 500). Se conserva la negociación JSON/HTML y limpieza de buffers que SimpleRest ya tiene. | ErrorHandlerStatusCodeTest.php: 1/1 prueba, 6 aserciones en PHP 8.1 y 8.4. Prueba el mapeo puro; no invoca el handler que termina el proceso. | 62aa3b418 |

| src/framework/Helpers/env.php, src/framework/Libs/System.php | A / D — `env()` da prioridad al entorno del proceso y luego al archivo cargado, incluso si el valor del proceso está vacío. `System::getPHP()` sigue leyendo `PHP_BINARY` con `Env::get()` para conservar la configuración desde archivo. `config/databases.php` usa `env()` para resolver conexiones; por tanto, el entorno del proceso ahora puede prevalecer sobre `.env`. | EnvHelperPrecedenceTest.php: 3/3 pruebas en PHP 8.1 y 8.4; una deprecación de firma preexistente en `Env::set()` aparece en PHP 8.4. Smoke aislado: `System::getPHP()` conserva el valor configurado en `Env::$data` frente a un valor de proceso. No se abrió conexión de base de datos. | efd9dee2f |
| src/framework/Libs/Mail.php, config/config.php | A — Se incorpora la lista de opciones PHPMailer admitidas para evitar asignaciones dinámicas o propiedades desconocidas y se corrige Debugutput a Debugoutput. | MailOptionsTest.php: 3/3 pruebas y 5 aserciones en PHP 8.1 y 8.4; no se envió correo. El vendor local usa PHPMailer 7.1.1. La restricción GoProp ^6.9 frente a SimpleRest ^7.1 sigue pendiente de resolver en Fase 2. | 51f462bd9 |
| src/framework/Api/Collections.php | A / C — Se portan la validación de entidad almacenada y la comparación entre entidad solicitada y almacenada al eliminar. No se incorporan nombres de tablas de pagos propios de GoProp. | CollectionEntityValidationTest.php: 4/4 pruebas en PHP 8.1 y 8.4; valida las decisiones antes de persistir y no abre conexión. | f2ccc392a |
| src/framework/Libs/i18n/Translate.php | A — Se portan normalización de locale, selección robusta con setLang(), cadena de fallback, combinación de catálogos PO base/regionales y generación con convertPot(). exportLangDef() coincide con GoProp. Se conserva el parser en catálogos compuestos aunque gettext esté activo. | TranslateConvergenceTest.php: 7/7 pruebas y 19 aserciones en PHP 8.1 y 8.4; fixtures, salida generada y caché quedan bajo un directorio temporal único. PHP 8.4 reporta 12 deprecaciones de firmas preexistentes; no se hizo bootstrap ni acceso a base de datos. | 17b2bc5e0, d748248b8 |
| src/framework/Helpers/loggers.php | A — `log_error()` consulta `log_errors`, independiente de `debug`. Un error de producción debe poder registrarse aunque no se habilite el modo de depuración. | LoggerErrorConfigurationTest.php: 2/2 pruebas, 3 aserciones en PHP 8.1 y 8.4; usa un archivo temporal aislado. | 73d92fe93 |
| src/framework/Interfaces/ISubResources.php | A — Se añade `bool $pristine = false` al contrato, que ya estaba implementado por `SubResourceHandler` y usado desde `QueryBuilderTrait` para omitir mutadores/transformers. | ISubResourcesContractTest.php: 1/1 prueba, 6 aserciones; confirma interfaz, trait y ApiController. | 73d92fe93 |
| src/framework/Libs/MailAuditor.php | E — Se corrige la ruta indicada en el comentario a `src/logs/mail/`, que coincide con `logDir()`. No cambió el destino real ni se creó ningún log. | Revisión de la ruta calculada desde el archivo; sin prueba de escritura. | 73d92fe93 |
| src/framework/Traits/ErrorReporting.php, src/framework/Traits/QueryBuilderTrait.php, src/framework/Traits/InsertWithSubResourcesTrait.php, src/framework/Model.php, src/framework/Libs/Logger.php, app/Modules/AndroidEngine/src/Libs/{AndroidCodeAnalyzer,CodeStackDetector}.php | A — Se retiran constantes de traits (no soportadas antes de PHP 8.2) y se definen en `Logger` y `Model`, conservando esos contratos públicos. AndroidCodeAnalyzer y CodeStackDetector declaran sus constantes de severidad porque consumen el trait. | FrameworkTraitConstantsTest.php y QueryBuilderOrderByContractTest.php: 9/9 pruebas, 20 aserciones en PHP 8.1 y 8.4. No se abrió DB. En PHP 8.4 siguen apareciendo deprecaciones previas por parámetros implícitamente nullable. | fa54c6a39 |

Los commits funcionales se enviaron a origin/master. No se modificaron manifiestos, tags ni el directorio simplerest-pack.

## Decisiones comprobadas y diferencias controladas

| Ruta | Clase | Decisión actual | Prueba / estado |
| --- | --- | --- | --- |
| Traits/QueryBuilderTrait.php | A | Portada la validación segura de `orderBy($field, $direction)` y conserva el arreglo asociativo. Además, mueve los modos de ejecución/DECIMAL_AS_STRING a `Model` para que el core pueda cargarse en PHP 8.1. | 6 pruebas de contrato de orderBy y 1 prueba de compatibilidad; PHP 8.1 y 8.4. |
| Traits/ErrorReporting.php, Traits/InsertWithSubResourcesTrait.php, Libs/Logger.php, Model.php | A / D — compatibilidad PHP | Las constantes que eran de trait ahora residen en las clases concretas que forman la API pública. `Logger::*` y `Model::*` se preservan; los consumidores del trait ErrorReporting en el módulo Android también declaran sus constantes. Un consumidor externo que aplicaba `ErrorReporting` directamente deberá declarar `SEVERITY_*`; era necesario para corregir la declaración PHP `>=8.1` sin elevarla a 8.2. | FrameworkTraitConstantsTest.php: 1 prueba, 11 aserciones; QueryBuilderOrderByContractTest.php: 6/6; todo pasa en PHP 8.1 y 8.4. Riesgo de compatibilidad para consumidores externos del trait documentado. |
| Request.php | A | Portadas las mejoras de lectura de cabeceras y MIME; se retienen los métodos inmutables que SimpleRest ya contiene. | 3 pruebas y smoke HTTP aprobados. |
| Libs/Url.php | A | Portados el error tipado de JSON y getAppUrl(), que usa app_url para enlaces públicos en peticiones y CLI. | JSON inválido probado en smoke HTTP; UrlAppUrlTest.php: 2/2 pruebas. |
| Exceptions/InvalidRequestBodyException.php | A | La excepción genérica ya existe en SimpleRest; se omitió la referencia documental específica de GoProp. | Estado HTTP 400 probado. |
| Model.php | B | Se conserva el código más amplio de SimpleRest (ModelRecord y helpers de registros); no se reemplaza por la versión reducida de GoProp. | Comparación de fuente; pruebas de persistencia no ejecutadas. |
| Libs/DBRels.php | E | El diff revisado solo elimina imports y corrige un comentario. No hay mejora funcional que portar. | Sin prueba necesaria para esta diferencia textual. |
| Schema.php | B / convergida | No aparece entre las rutas divergentes actuales. La rama PostgreSQL de hasTable() ya está en ambos blobs versionados. | La consulta de metadatos no se ejecutó contra una base de datos en esta fase. |
| FrontController.php | A — conflicto reconciliado | Invitado sin método callable recibe 401; usuario autenticado sin permiso recibe 403. El estado autenticado se consulta después de que ApiController valide el token. Se conserva la distinción ACL en lugar de aplicar el 401 incondicional de GoProp. | FrontControllerAuthorizationStatusTest.php valida ambos códigos; ApiAuthorizationDispatchTest.php valida el cálculo de permisos y callables. 11/11 pruebas combinadas en PHP 8.1 y 8.4; no hay prueba HTTP end-to-end de esta rama. |
| Libs/CommandRegistry.php | A / D | A — Portada la ayuda de grupo cuando no hay subcomando, evitando ejecución implícita del primer comando. D — Falta resolver precedencia aplicación/extensiones/framework, colisiones y aliases; además, buildSearchPaths() no descubre comandos bajo Composer vendor/. | CommandRegistryDispatchTest.php: 2/2 pruebas, 11 aserciones; resolución Composer y colisiones pendientes para Fase 2. |
| Libs/CorsHandler.php | A | Portado el contrato HTTP de GoProp: Referer no sustituye Origin; si Origin no existe, CORS no interviene. Un Origin presente que no esté permitido conserva el 403. | CorsHandlerOriginTest.php: 2/2 pruebas, 4 aserciones en PHP 8.1 y 8.4. |
| Helpers/env.php | A | Portada la precedencia de GoProp: entorno del proceso, archivo cargado y valor por defecto; una cadena vacía del proceso es explícita. | EnvHelperPrecedenceTest.php: 3/3 pruebas en PHP 8.1 y 8.4. |
| Libs/System.php | D — reconciliado | `System::getPHP()` lee `PHP_BINARY` con `Env::get()` como GoProp; el valor del proceso no oculta el valor configurado en archivo. | Smoke aislado con valores distintos en proceso y `Env::$data`; no usa base de datos. |
| Libs/Env.php | E | La asignación inicial desde `$_ENV` en SimpleRest queda reemplazada por `parseFile()` en la ruta normal de `setup()`; GoProp la elimina como código sin efecto en esa ruta. Se conserva por ahora, sin portar comportamiento. | Comparación del flujo `setup()`; sin cambio de código ni prueba adicional. |
| Libs/FileUploader.php | B | SimpleRest conserva whitelist de extensión/MIME y protección del directorio. GoProp elimina ambas y permite almacenar tipos no validados; no se porta esa regresión de seguridad. | Revisión comparativa de `__construct()`, `setLocation()` y `save()`; no se cambió la implementación. |
| Api/Collections.php | A / C | Portadas las comprobaciones de entidad almacenada y la verificación de entidad al eliminar, antes de persistencia. Las tablas de pagos y liquidaciones añadidas por GoProp siguen siendo propias de la aplicación. | CollectionEntityValidationTest.php: 4/4 pruebas en PHP 8.1 y 8.4; sin conexión de base de datos. |
| Libs/Mail.php | A / D | Portada una lista explícita de opciones PHPMailer compatibles con la versión instalada; se corrige el nombre de Debugoutput. El conflicto entre PHPMailer ^6.9 en GoProp y ^7.1 en SimpleRest pertenece a los contratos Composer de Fase 2. | MailOptionsTest.php: 3/3 pruebas, 5 aserciones en PHP 8.1 y 8.4; no se envió correo. |
| Libs/i18n/Translate.php | A | Portadas normalización, selección de locales, fallback/merge de catálogos y generación de catálogos. exportLangDef() es igual en ambos repositorios. | TranslateConvergenceTest.php: 7/7 pruebas, 19 aserciones en PHP 8.1 y 8.4. |
| Api/ApiController.php | B / C | Se conserva el control genérico de callables y ACL de SimpleRest. La integración de GoProp con ImpersonationManager depende del subsistema de impersonación propio de GoProp y queda fuera del core. | ApiAuthorizationDispatchTest.php valida despacho y permisos: 11/11 pruebas junto con FrontControllerAuthorizationStatusTest.php; no hubo cambios de seguridad en este paso. |
| Api/AuthController.php | B / C | SimpleRest conserva la validación opcional en el lookup de login. Los flujos persistentes/auditados de impersonación en GoProp llaman a clases de su subsistema propio; siguen siendo responsabilidad de GoProp. | Comparación de llamadas y dependencias; sin modificación de autenticación ni prueba de integración con tokens/DB en esta revisión. |
| Handlers/ApiHandler.php | B / D | Se conserva la ruta de namespace Controllers\Api de SimpleRest. GoProp cambió a Controllers\api aunque sus controladores versionados están en app/Controllers/Api; la variante de minúsculas no se copia por el riesgo de resolución PSR-4 en sistemas sensibles a mayúsculas. | Inspección de namespace, autoload y rutas versionadas; no se ejecutó una carga de controlador en Linux. |
| Handlers/ErrorHandler.php | A / B | Portada la validación de rango del código HTTP desde GoProp; se mantienen la respuesta HTML/JSON y el vaciado de buffers ya presentes en SimpleRest. | ErrorHandlerStatusCodeTest.php cubre seis códigos límite en PHP 8.1 y 8.4; no prueba el exit del response handler. |
| Security/Acl.php, Security/Compiler/EffectivePermissionCompiler.php | B / C / D | SimpleRest conserva el rechazo de `*` como recurso. impersonate_write y el aplazamiento de persistencia están ligados a impersonación y al AclMakeCommand de GoProp. El reconciliador del paquete existente fine-grained-acl borra y recrea roles dentro de una transacción y restaura asignaciones; se mantiene como dueño esa integración opcional, con revisión de invariantes y pruebas aisladas MySQL/PostgreSQL pendiente antes de reusarla. No se ejecutó ninguna escritura de base de datos. | AclWildcardSemanticsTest.php: 6/6 pruebas, 8 aserciones en PHP 8.1 y 8.4; la reconciliación del catálogo no está cubierta en SimpleRest. |
| Response.php | A / B / C | A — Conserva metadatos hermanos y regenera los campos reservados del envelope. B — Mantiene el emisor de status HTTP de SimpleRest. C — No incorpora la auditoría de mutaciones de impersonación, que depende de GoProp. | unit-tests/http/ResponseEnvelopeMetadataTest.php: 3/3 pruebas, 10 aserciones; no se ejecutaron rutas ni conexiones. |
| Validator.php | A | Portado skipBoundsChecks() para que los filtros de lectura omitan solo los límites de escritura y mantengan la validación de tipo. | 2 pruebas focalizadas aprobadas. |
| Libs/Files.php | A | Se conserva la ruta solicitada en el mensaje cuando realpath() devuelve false. | FilesWritablePathTest.php: 1/1 prueba, 3 aserciones en Linux/WSL. |
| Libs/LangDetector.php | A | Se incorporan detección por idioma y soporte genérico italiano, hindi y bengalí; se omiten palabras de dominio de GoProp. | LangDetectorTest.php: 4/4 pruebas, 9 aserciones en PHP 8.1 y 8.4. |
| Helpers/loggers.php | A — convergida | `log_error()` usa la opción `log_errors`; `debug` sigue gobernando logger(), dump() y log_sql(). La diferencia restante de blob es solo el salto final de línea. | LoggerErrorConfigurationTest.php: 2/2 pruebas, 3 aserciones en PHP 8.1 y 8.4; usa un archivo temporal aislado. |
| Helpers/view.php, View.php | D — resuelta para el host SimpleRest | Se conserva `/public/assets/`, coherente con el `DocumentRoot` del proyecto raíz descrito por QuickStart y con el árbol `public/assets/`. GoProp cambió a `/assets/`, pero su `.htaccess` documenta la raíz del proyecto como docroot local y no existe `assets/` en la raíz; ese supuesto no se traslada al core. | AssetPathTest.php: 1/1 aserción con `app_url` aislada. La salida de `View::js/css` usa el mismo prefijo. La app GoProp se verificará al consumir el core canónico. |
| Interfaces/ISubResources.php | A — convergida | El contrato declara el parámetro opcional `pristine` que ya implementa el trait y entrega QueryBuilder al obtener relaciones. El blob aún difiere por el salto final de línea; el contrato ya coincide. | ISubResourcesContractTest.php: 1/1 prueba, 6 aserciones; sin acceso a base de datos. |
| Libs/GoogleDrive.php | D — se conserva SimpleRest | GoProp sustituye `env()` por `Env::get()`. El helper actual prioriza valores del proceso (`getenv`) sobre el archivo; se conserva para que `APP_ENV` externo gobierne la rama de seguridad SSL. No se adopta la lectura solo desde archivo. | EnvHelperPrecedenceTest.php cubre la precedencia. No se inicializó Google SDK ni hubo petición externa. |
| Libs/MailAuditor.php | E | GoProp corrigió solo la descripción de `logDir()`; el comentario de SimpleRest se alineó con la ruta real `src/logs/mail/`. | Revisión estática; no se escribió ningún log. |
| Libs/PostmanGenerator.php | C / D — se conserva SimpleRest | GoProp usa `password123` como contraseña por defecto para sus ejemplos de registro/login. No se incorpora una credencial conocida en colecciones que podrían apuntar a un API real; las aplicaciones deben pasar sus valores de fixture explícitamente. | No hubo cambio de autenticación ni se generó/ejecutó una colección. |

Las 34 rutas comunes con blob distinto tienen una decisión A–E. Dos blobs difieren únicamente por el salto de línea final después de alinear sus contratos (`Helpers/loggers.php` e `Interfaces/ISubResources.php`). Las decisiones D conservan límites explícitos que requieren gate o evidencia de consumidor: resolución Composer de comandos, uso externo del trait ErrorReporting, valores de entorno para Google Drive, rutas de assets del host y credenciales de ejemplo.

| Ruta | Estado |
| --- | --- |
| Api/ApiController.php | Portada `skipBoundsChecks()` para filtros de lectura; se conserva el control genérico de ACL de SimpleRest. La integración de impersonación de GoProp permanece en su aplicación. |
| Libs/RedisLock.php | GoProp-only. Sigue en GoProp: no se halló uso productivo en la aplicación; el prefijo predeterminado `goprop:` y el fallback no atómico de liberación requieren revisión de seguridad/concurrencia antes de promoverlo al core o a un paquete opcional existente. |

## Clases exclusivas

**Solo en GoProp (12):**

- Las 11 clases de Libs/Impersonation/ parecen ligadas al producto y a sus límites de seguridad. Su propietario provisional es GoProp; no se trasladan al core genérico sin evidencia de reutilización y una revisión de sus controles de mutación.
- Libs/RedisLock.php es un helper de bloqueo con phpredis opcional. Solo se encontró en una ruta de prueba/diagnóstico y en sus tests, no en servicios de GoProp; su prefijo predeterminado `goprop:` lo acopla a la aplicación y `unsafeReleaseFallback()` hace `GET` y `DEL` separados. Se conserva en GoProp hasta que se diseñe y pruebe un mecanismo de liberación atómico y exista un consumidor genérico.

**Solo en SimpleRest (10):**

- ModelRecord.php: API genérica de modelo que explica parte de la diferencia de Model.php; se conserva en SimpleRest.
- Interfaces/IWebhookTransport.php y las clases Webhook*: capacidades ya presentes en SimpleRest; no se copian a GoProp durante la convergencia del core.
- Collections.php y Templates/description.txt: propietario y necesidad de incluirlos en el artefacto de Composer se revisarán al validar el paquete.

## Pruebas ejecutadas y límites

- QueryBuilderOrderByContractTest.php: 6/6.
- RequestInputParsingTest.php: 3/3.
- ValidatorSkipBoundsContractTest.php: 2/2.
- ResponseEnvelopeMetadataTest.php: 3/3, 10 aserciones; incluye `data: null`, metadatos y payload escalar.
- FilesWritablePathTest.php: 1/1, 3 aserciones en WSL/PHP 8.1; el caso confirma la rama Unix sin escribir archivos.
- LangDetectorTest.php: 4/4, 9 aserciones en PHP 8.1 y 8.4; valida texto vacío, idiomas latinos e identificación de escrituras hindi y bengalí.
- UrlAppUrlTest.php: 2/2 pruebas en PHP 8.1 y 8.4; valida URL configurada sin barra final y URL vacía.
- CommandRegistryDispatchTest.php: 2/2 pruebas, 11 aserciones en PHP 8.1 y 8.4; los flags sin comando no ejecutan comandos.
- CorsHandlerOriginTest.php: 2/2 pruebas, 4 aserciones en PHP 8.1 y 8.4; sin Origin no toca status/cabeceras y Origin no permitido sigue en 403.
- FrontControllerAuthorizationStatusTest.php + ApiAuthorizationDispatchTest.php: 11/11 pruebas, 78 aserciones en PHP 8.1 y 8.4; valida ambos estados 401/403 y la regresión ACL, sin conexión a base de datos.
- ErrorHandlerStatusCodeTest.php: 1/1 prueba, 6 aserciones en PHP 8.1 y 8.4; valida el mapeo de códigos de excepción a status HTTP sin invocar el handler que termina el proceso.
- EnvHelperPrecedenceTest.php: 3/3 pruebas en PHP 8.1 y 8.4; verifica proceso > archivo y valor vacío explícito. PHP 8.4 informa la deprecación previa de `Env::set()`. Un smoke separado confirma que `System::getPHP()` mantiene el valor cargado desde archivo; no se conecta a una base de datos.
- MailOptionsTest.php: 3/3 pruebas, 5 aserciones en PHP 8.1 y 8.4; validación de opciones, sin envío SMTP.
- CollectionEntityValidationTest.php: 4/4 pruebas en PHP 8.1 y 8.4; comprobaciones de entidad sin acceso a base de datos.
- TranslateConvergenceTest.php: 7/7 pruebas, 19 aserciones en PHP 8.1 y 8.4; normalización, fallback heredado/compuesto, mezcla de PO, modo gettext en catálogos compuestos y generación aislada de catálogos. PHP 8.4 informa 12 deprecaciones de firmas preexistentes en Translate.php.
- LoggerErrorConfigurationTest.php, AssetPathTest.php e ISubResourcesContractTest.php: 4/4 pruebas, 10 aserciones en PHP 8.4; AssetPathTest e ISubResourcesContractTest: 2/2 pruebas, 7 aserciones en PHP 8.1.
- FrameworkTraitConstantsTest.php + LoggerErrorConfigurationTest.php + QueryBuilderOrderByContractTest.php: 9/9 pruebas, 20 aserciones en PHP 8.1 y 8.4. Prueba que Model, Logger y el módulo Android exponen sus constantes y que se pueden cargar sin constantes declaradas en traits. PHP 8.4 aún informa deprecaciones por firmas implícitamente nullable preexistentes en QueryBuilderTrait y clases que carga el test.
- php -l en todos los PHP modificados: sin errores.
- Servidor PHP built-in local: POST de JSON inválido con charset=utf-8 → HTTP 400 y respuesta JSON genérica.
- No se ejecutó la suite completa ni pruebas que conecten a MySQL/PostgreSQL; no se configuró ni se modificó ninguna base de datos.
- La compatibilidad de GoProp como consumidor Composer aún no está demostrada. No se modificó su composer.json ni se cambió de rama.
- El bloqueo de PHP 8.1 encontrado al cargar Logger se corrigió en fa54c6a39 moviendo constantes desde ErrorReporting, QueryBuilder e InsertWithSubResources a las clases concretas. `rg` confirma que ya no quedan declaraciones de constantes dentro de `src/framework/Traits/`; las pruebas enfocadas cargan Logger y Model en PHP 8.1. El riesgo restante es de API para terceros que usan directamente ErrorReporting como trait sin declarar las constantes que antes heredaban.

## Puerta de salida de Fase 1

Las 34 diferencias comunes y las 22 rutas exclusivas tienen disposición y propietario. Las mejoras genéricas aprobadas están en SimpleRest; las capacidades de impersonación siguen en GoProp y `RedisLock` permanece allí por su acoplamiento y liberación no atómica. Las pruebas focalizadas de core pasan en PHP 8.1 y 8.4; no se usó una base de datos. Esto cierra la puerta de convergencia del core.

Fase 2 comienza con las identidades Composer ya existentes: `boctulus/simplerest`, `boctulus/simplerest-pdf`, `boctulus/simplerest-code-tools`, `boctulus/basic-acl` y `boctulus/fine-grained-acl`. Hay que validar cuáles están realmente publicados y ajustar sus límites/manifiestos sin duplicar nombres. No modificar GoProp hasta que el conjunto existente pase instalación limpia de consumidor y las pruebas de CLI/autoload.
