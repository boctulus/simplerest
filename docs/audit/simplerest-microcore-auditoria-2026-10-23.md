# Auditoría de microcore de SimpleRest — inventario de extracción

**Fecha:** 2026-10-23  
**Fuente:** GitHub `boctulus/simplerest`, rama `fix/composer-distribution-modularization-recovered-20261022`, commit `491f665cc8bc2cdd85b76159da6e662958cbdbee`.  
**Estado al redactar:** auditoría de código realizada en modo lectura; en el commit fuente todavía no se habían aplicado las extracciones. **Estado en `master`, 2026-10-23:** se aplicó una primera reducción del kernel y se archivaron candidatos; no se modificó GoProp ni se publicaron versiones nuevas.
**Alcance:** todos los 270 archivos versionados bajo `src/framework/` en este commit. Los destinos son propuestas de arquitectura, **no pruebas de que cada archivo sea seguro de mover sin refactor**. Una parte de las clases se inspeccionó en código fuente; el resto está clasificado de forma conservadora por ruta y función, pendiente de grafo de dependencias y consumidores.

## Regla de arquitectura

El microcore debe poder procesar una petición HTTP y responder JSON, sin PDO, JWT, ACL, Symfony, FPDF, Bootstrap, HTML, SMTP, cURL, SDKs, trabajos, plantillas, generadores ni paquetes opcionales instalados.

**Prohibido:** alias `class_alias()` de extensiones en el core, shims que mencionan el nombre de un paquete, `autoload.files` registrando aliases, excepciones del core que aconsejan instalar paquetes concretos, paquetes opcionales en `suggest`, clases de aplicaciones referenciadas por la implementación base. El core no puede depender de una extensión, ni siquiera indirectamente.

**Permitido:** `ServiceProvider` genérico, contenedor DI, contratos de middleware/rutas/eventos de extensión, autoload Composer estándar, y proveedores registrados voluntariamente desde cada paquete/host.

**Bootstrap legacy:** no presentarlo como extensión principal moderna. El soporte Bootstrap 5/AdminLTE es legado opt-in para consumidores existentes; en particular GoProp, que usa Bt5Form. No mover `BootstrapPaginator` de un cajón de integraciones a otro cajón generalista. Puede tener su paquete legacy claramente denominado, pero no ser parte de runtime JSON.

## Bloqueos arquitectónicos demostrados en fuente

1. `src/framework/Compatibility/OptionalPackageClassAliases.php`: autoloader de **18 FQCN** de extensiones concretas. Eliminar. Existen además 16 archivos shim físicos bajo `src/framework/Libs/` y subcarpetas. `BootstrapPaginator.php` en esta rama solo hace `class_alias()`; el cuerpo está en `simplerest-integrations`.
2. `composer.json`: `autoload.files` fuerza el autoloader de aliases; `suggest` enumera extensiones concretas; `autoload-dev` y `require-dev` mezclan proyectos opcionales con el manifiesto de core. Usar un workspace de desarrollo separado sin contaminar el artefacto core.
3. `src/framework/Response.php`: dependencia directa `DB::rollbackAndCloseAllConnections()`; lógica de paginación y configuración de API. Separar rollback por hook/stack controlado por la extensión DB; `Response` se limita a HTTP+JSON.
4. `src/framework/Libs/Config.php`: `setup()` incluye de forma fija `config/databases.php` además de `config/config.php`; una aplicación sin base de datos debe arrancar sin DB config.
5. `src/framework/Libs/Factory.php`: construye concretamente `MyAuthController` de la aplicación, conoce `Acl` y `Validator`. Sustituir por bindings/contratos o eliminar Factory.
6. `src/framework/Handlers/OutputHandler.php`: importa `ApiController` y `ConsoleController`. Separar salida del kernel de API/CLI.
7. `src/framework/Api/ApiController.php`: gran clase de ~2.616 líneas que cruza PDO, DB, ACL, Auth, webhooks, validación y modelos. Pasar al paquete REST opcional y desenredar dependencias por DI.
8. `src/framework/Request.php`: métodos de autorización, identidad, tenant y paginación en la clase HTTP. Extraer capacidades secundarias.
9. `src/framework/FrontController.php`: contiene políticas de autenticación/status y pipeline especializado. Refactor al despachador genérico y middleware extensible.
10. `src/framework/Helpers/`: se carga como conjunto según `config/autoload.php`. Los helpers `gdrive`, `wp_dummies`, `psr7`, `view`, `i18n`, `db`, `system`, etc. no deben cargarse como parte del kernel.
11. `src/framework/Psr7/*.php` y `Helpers/psr7.php`: adaptadores opcionales; por tanto `psr/http-message` no tiene por qué ser dependencia de runtime del core.
12. `src/framework/Security/` + interfaces ACL: no es indispensable para una ruta HTTP pública mínima. Mantener seguridad fail-closed: una ruta declarada protegida no se sirve sin el proveedor requerido.
13. `src/framework/Constants.php`: contiene rutas rígidas hacia `app/`, `Views/`, `Migraciones/`, `Commands/`, `assets/` y asume la ubicación física del código dentro de una distribución; sustituir por un bootstrap con raíz de aplicación suministrada por el consumidor, no por constantes de producto.
14. `src/framework/Collections.php` consta solamente de un `use` fuera de la etiqueta PHP. Revisar y eliminar como artefacto roto/obsoleto.
15. `src/framework/Templates/` (plantillas, ejemplos y scaffolding): scaffolding y ejemplos, no código runtime. Su propiedad debe estar en paquete de generadores o starter, no en una dependencia de producción.
16. `Libs/HTML`, `CSS`, `HtmlBuilder`, `EasyHTMLTable` y frontends Bootstrap/AdminLTE: fuera del microcore; lo mismo ocurre con `Image`, `Imaginator`, `JsonLd`, `SiteMap`.
17. Código específico de proveedor aún dentro de `Libs/`: `GoogleMaps`, `GeoLite2`, `SendinBlue`, `MailFromRemoteWP`, `Pastebin`, `WP`. Son integraciones/aplicaciones, no core.

## Plan de migración sin fallbacks

**Fase 0.** Congelar contrato microcore: petición -> routing -> middleware -> JSON, sin extensiones. Crear consumidor aislado `composer require boctulus/simplerest` con `--no-dev` y prohibir dependencias de features; añadir prueba inversa: core no puede referenciar namespace/clases/paquetes externos opcionales.

**Fase 1.** Eliminar los 18 mapeos de alias y 16 shims físicos. Eliminar `autoload.files` de compatibilidad y referencias en `suggest`. Arreglar dependencias inversas `Response -> DB`, `Config -> databases.php`, `Factory -> MyAuthController/Acl`, `OutputHandler -> ApiController`, y helpers auto-cargados. No dejar un wrapper en core que cargue otro package.

**Fase 2.** Extraer por dependencia real, en este orden: DB/ORM/Schema (incluidos tests); REST automático y controladores de recurso; Auth/ACL; PSR7; mails/HTTP client/webhooks; view/Bootstrap/i18n; CLI/scaffolding; utilidades y proveedores específicos.

**Fase 3.** Consumidores: crear pruebas de instalación por extensión desde Composer; GoProp sólo se migra en rama específica después de verificar `Bt5Form` y overrides (`ApiController`, `AuthController`, `Collections`, `Response`). Sin forzar GoProp a la nueva versión mientras no pase integración funcional.

**Fase 4.** Documentar catálogo real con ejemplos y alcance explícito; no crear paquetes minúsculos automáticamente ni publicar código con problemas de seguridad o contratos de prueba insuficientes.

## Revisión de dependencias necesarias antes de mover

- Inspeccionar `use`, `new`, `extends`, `implements`, `require`, helpers globales y proveedores dinámicos; la inspección de referencias estáticas no detecta toda la invocación indirecta.
- Clasificar consumidores **internos**, `app/` del monorepo y **externos** (GoProp). Generar secuencia de movimientos topológicamente válida sin dependencias cíclicas.
- Eliminar los imports concretos de features desde clases KEEP/REWRITE; un `class_exists` o `LogicException` con nombre de paquete es una violación, no una solución.
- Para cualquier símbolo ambiguo, regla por defecto: **fuera del core** salvo prueba positiva de necesidad para el smoke mínimo.
- Las etiquetas `EXTRACT_OR_ARCHIVE` son destino de decisión; no implican crear un repositorio Composer individual por archivo.

## Resumen por disposición

- **DELETE:** 17 archivos
- **EXTRACT_OR_ARCHIVE:** 221 archivos
- **KEEP:** 7 archivos
- **REFACTOR:** 25 archivos

**Total:** 270 archivos.

## Inventario exhaustivo por destino candidato

### KEEP_KERNEL — 7 archivos

Kernel infrastructure candidate; verify minimal JSON request smoke and no optional dependencies..

- `src/framework/Container.php`
- `src/framework/Exceptions/BaseException.php`
- `src/framework/Exceptions/InvalidRequestBodyException.php`
- `src/framework/Exceptions/MiddlewareNotFoundException.php`
- `src/framework/Interfaces/IServiceProvider.php`
- `src/framework/Middleware.php`
- `src/framework/ServiceProvider.php`

### REWRITE_KERNEL — 25 archivos

Currently coupled or oversized; retain only the strictly necessary kernel portion, move the rest..

- `src/framework/Constants.php`
- `src/framework/FrontController.php`
- `src/framework/Handlers/ErrorHandler.php`
- `src/framework/Handlers/MiddlewareHandler.php`
- `src/framework/Handlers/OutputHandler.php`
- `src/framework/Handlers/RequestHandler.php`
- `src/framework/Helpers/config.php`
- `src/framework/Helpers/factories.php`
- `src/framework/Helpers/http.php`
- `src/framework/Helpers/package.php`
- `src/framework/Helpers/polyfill.php`
- `src/framework/Interfaces/Arrayable.php`
- `src/framework/Libs/Arrays.php`
- `src/framework/Libs/Config.php`
- `src/framework/Libs/Factory.php`
- `src/framework/Libs/Files.php`
- `src/framework/Libs/Logger.php`
- `src/framework/Libs/MediaType.php`
- `src/framework/Libs/Msg.php`
- `src/framework/Libs/Reflector.php`
- `src/framework/Libs/Strings.php`
- `src/framework/Libs/Url.php`
- `src/framework/Request.php`
- `src/framework/Response.php`
- `src/framework/WebRouter.php`

### REMOVE_SHIM — 17 archivos

Remove old-path shim / optional-package alias: no legacy fallback or hardcoded extension dependency in core..

- `src/framework/Compatibility/OptionalPackageClassAliases.php`
- `src/framework/Libs/BootstrapPaginator.php`
- `src/framework/Libs/CMS_Scanner/CMSs/Magento.php`
- `src/framework/Libs/CMS_Scanner/CMSs/PrestaShop.php`
- `src/framework/Libs/CMS_Scanner/CMSs/Shopify.php`
- `src/framework/Libs/CMS_Scanner/CMSs/Squarespace.php`
- `src/framework/Libs/CMS_Scanner/CMSs/Wix.php`
- `src/framework/Libs/CMS_Scanner/CMSs/WordPress.php`
- `src/framework/Libs/CMS_Scanner/Scanner.php`
- `src/framework/Libs/DomCrawler.php`
- `src/framework/Libs/GoogleDrive.php`
- `src/framework/Libs/HuggingFace.php`
- `src/framework/Libs/OneSignal.php`
- `src/framework/Libs/WooCommerce.php`
- `src/framework/Libs/WooCommerceApiClient.php`
- `src/framework/Libs/code_cleaner/AngularCleaner.php`
- `src/framework/Libs/code_cleaner/BootstrapCleaner.php`

### NEEDS_CALLGRAPH_EXTRACT_BY_DEFAULT — 1 archivos

Not evidenced as indispensable; default is extract/archive, verify callers before making final move..

- `src/framework/Collections.php`

### acl — 17 archivos

Move ACL implementation and contracts to ACL package(s); core accepts optional middleware hook..

- `src/framework/FoldersAclExtension.php`
- `src/framework/Interfaces/IAcl.php`
- `src/framework/Interfaces/IAclBuilder.php`
- `src/framework/Interfaces/IAclPolicy.php`
- `src/framework/Interfaces/IAclRuntime.php`
- `src/framework/Security/Acl.php`
- `src/framework/Security/Compiler/EffectivePermissionCompiler.php`
- `src/framework/Security/Compiler/TbPermissionBits.php`
- `src/framework/Security/Contracts/AclEngineInterface.php`
- `src/framework/Security/Contracts/AuthorizationPolicyInterface.php`
- `src/framework/Security/Contracts/AuthorizationServiceInterface.php`
- `src/framework/Security/Domain/AclContext.php`
- `src/framework/Security/Domain/CapabilityTypeResolver.php`
- `src/framework/Security/Engine/AclEngine.php`
- `src/framework/Security/Explanation/PermissionExplanation.php`
- `src/framework/Security/Service/RoleHierarchyService.php`
- `src/framework/Security/Snapshot/AclSnapshot.php`

### api-rest — 11 archivos

Move to optional REST resource/automatic endpoints extension; not required for a minimal route..

- `src/framework/Api/ApiController.php`
- `src/framework/Api/Collections.php`
- `src/framework/Api/Download.php`
- `src/framework/Api/Files.php`
- `src/framework/Api/MySelf.php`
- `src/framework/Api/ResourceController.php`
- `src/framework/Api/TrashCan.php`
- `src/framework/Controllers/Controller.php`
- `src/framework/Handlers/ApiHandler.php`
- `src/framework/Interfaces/IApi.php`
- `src/framework/Traits/SubResourceHandler.php`

### auth — 7 archivos

Move to optional authentication/session/identity extension..

- `src/framework/Api/AuthController.php`
- `src/framework/Handlers/AuthHandler.php`
- `src/framework/Interfaces/IAuth.php`
- `src/framework/Libs/Cookie.php`
- `src/framework/Libs/Obfuscator.php`
- `src/framework/Libs/Session.php`
- `src/framework/Libs/SimpleCrypt.php`

### bootstrap-legacy — 2 archivos

Move framework HTML builder primitives out; keep Bootstrap-specific code in opt-in legacy UI package..

- `src/framework/Libs/HtmlBuilder/Html.php`
- `src/framework/Libs/HtmlBuilder/Tag.php`

### cache — 7 archivos

Move caching/memoization; retain only any minimal interfaces if absolutely required..

- `src/framework/Helpers/cache.php`
- `src/framework/Interfaces/ICache.php`
- `src/framework/Interfaces/IMemoization.php`
- `src/framework/Libs/Cache.php`
- `src/framework/Libs/FileCache.php`
- `src/framework/Libs/InMemoryCache.php`
- `src/framework/Libs/Memoization.php`

### cli-codegen — 48 archivos

Move CLI developer commands, generators and template scaffolding; never runtime core..

- `src/framework/CliRouter.php`
- `src/framework/Commands/BaseCommand.php`
- `src/framework/Controllers/ConsoleController.php`
- `src/framework/Helpers/debug.php`
- `src/framework/Helpers/system.php`
- `src/framework/Interfaces/ICommand.php`
- `src/framework/Libs/Cli.php`
- `src/framework/Libs/CodeReducer.php`
- `src/framework/Libs/CommandRegistry.php`
- `src/framework/Libs/MockNames.php`
- `src/framework/Libs/PHPLexicalAnalyzer.php`
- `src/framework/Libs/PostmanGenerator.php`
- `src/framework/Libs/StdOut.php`
- `src/framework/Libs/SystemMessages.php`
- `src/framework/Libs/VarDump.php`
- `src/framework/Templates/ApiRestfulController.php`
- `src/framework/Templates/Command.php`
- `src/framework/Templates/ConsoleController.php`
- `src/framework/Templates/Controller.php`
- `src/framework/Templates/CronJob.php`
- `src/framework/Templates/DTO.php`
- `src/framework/Templates/Exception.php`
- `src/framework/Templates/FreshCopy/app/Views/home.php`
- `src/framework/Templates/FreshCopy/config/routes.php`
- `src/framework/Templates/Helper.php`
- `src/framework/Templates/Interface.php`
- `src/framework/Templates/Lib.php`
- `src/framework/Templates/Middleware.php`
- `src/framework/Templates/Migration.php`
- `src/framework/Templates/Migration_New.php`
- `src/framework/Templates/Model.php`
- `src/framework/Templates/ModelWithoutSchema.php`
- `src/framework/Templates/Module/Main.php`
- `src/framework/Templates/Module/config/config.php`
- `src/framework/Templates/Module/config/routes.php`
- `src/framework/Templates/Module/src/ModuleProvider.php`
- `src/framework/Templates/PackageConfig.php`
- `src/framework/Templates/Page.php`
- `src/framework/Templates/Schema.php`
- `src/framework/Templates/ServiceProvider.php`
- `src/framework/Templates/Task.php`
- `src/framework/Templates/TestCase.php`
- `src/framework/Templates/Trait.php`
- `src/framework/Templates/UpdateBatch.php`
- `src/framework/Templates/description.txt`
- `src/framework/Traits/CommandTrait.php`
- `src/framework/Traits/UnitTestCaseRunAllTrait.php`
- `src/framework/Traits/UnitTestCaseSQLTrait.php`

### database — 26 archivos

Move PDO DB/Schema/ORM/migrations/query builders to optional database extension..

- `src/framework/Exceptions/ColumnTableNotFoundException.php`
- `src/framework/Exceptions/EmptySchemaException.php`
- `src/framework/Exceptions/SchemaException.php`
- `src/framework/Exceptions/SqlException.php`
- `src/framework/Exceptions/TableAlreadyExistsException.php`
- `src/framework/Exceptions/TableNotFoundException.php`
- `src/framework/Helpers/db.php`
- `src/framework/Interfaces/ICountable.php`
- `src/framework/Interfaces/IDbAccess.php`
- `src/framework/Interfaces/IMigration.php`
- `src/framework/Interfaces/ISchema.php`
- `src/framework/Interfaces/ISubResources.php`
- `src/framework/Interfaces/IUpdateBatch.php`
- `src/framework/Libs/DB.php`
- `src/framework/Libs/DBCache.php`
- `src/framework/Libs/DBRels.php`
- `src/framework/Libs/Migration.php`
- `src/framework/Libs/MySQLDatabaseBackup.php`
- `src/framework/Libs/Paginator.php`
- `src/framework/Libs/Schema.php`
- `src/framework/Libs/Update.php`
- `src/framework/Model.php`
- `src/framework/ModelRecord.php`
- `src/framework/Traits/InsertWithSubResourcesTrait.php`
- `src/framework/Traits/QueryBuilderTrait.php`
- `src/framework/Traits/RelationshipTrait.php`

### events — 4 archivos

Move event bus/observer infrastructure out unless a specific kernel extension hook absolutely requires it..

- `src/framework/Interfaces/IObserver.php`
- `src/framework/Interfaces/ISubject.php`
- `src/framework/Libs/EventBus.php`
- `src/framework/Traits/EventBusTrait.php`

### extensions — 3 archivos

Move optional feature machinery; keep only generic ServiceProvider/Container contracts in microcore..

- `src/framework/Interfaces/IMyArrayAccess.php`
- `src/framework/Interfaces/IProcessable.php`
- `src/framework/Libs/Module.php`

### files-media — 9 archivos

Move upload/media/zip functionality to opt-in file/media package..

- `src/framework/Exceptions/FileNotFoundException.php`
- `src/framework/Exceptions/NotFileButDirectoryException.php`
- `src/framework/Exceptions/UnreadableFileException.php`
- `src/framework/Libs/Base64Uploader.php`
- `src/framework/Libs/FileUploader.php`
- `src/framework/Libs/Image.php`
- `src/framework/Libs/Imaginator.php`
- `src/framework/Libs/MetadataParser.php`
- `src/framework/Libs/ZipManager.php`

### http-client — 3 archivos

Move outbound HTTP and transport clients to HTTP client extension..

- `src/framework/Libs/ApiClient.php`
- `src/framework/Libs/CookieJar.php`
- `src/framework/Libs/Curl.php`

### http-features — 2 archivos

Move HTTP feature middleware such as CORS and SSE; core remains protocol/runtime only..

- `src/framework/Libs/CorsHandler.php`
- `src/framework/Libs/SSE.php`

### i18n — 5 archivos

Move translations and language detection to opt-in i18n package..

- `src/framework/Helpers/i18n.php`
- `src/framework/Libs/LangDetector.php`
- `src/framework/Libs/i18n/AlternativeGetTextTranslator.php`
- `src/framework/Libs/i18n/POParser.php`
- `src/framework/Libs/i18n/Translate.php`

### integrations — 6 archivos

Move vendor-specific/web integrations, or archive unmaintained implementations..

- `src/framework/Helpers/gdrive.php`
- `src/framework/Helpers/wp_dummies.php`
- `src/framework/Libs/GeoLite2.php`
- `src/framework/Libs/GoogleMaps.php`
- `src/framework/Libs/Pastebin.php`
- `src/framework/Libs/WP.php`

### jobs — 5 archivos

Move background jobs/scheduler/queue to optional package..

- `src/framework/Libs/BackgroundService.php`
- `src/framework/Libs/CronJob.php`
- `src/framework/Libs/CronJobMananger.php`
- `src/framework/Libs/JobQueue.php`
- `src/framework/Libs/Task.php`

### mail — 7 archivos

Move mail, SMTP and specific provider implementations to extensions..

- `src/framework/Interfaces/IMail.php`
- `src/framework/Libs/EmailTemplate.php`
- `src/framework/Libs/Mail.php`
- `src/framework/Libs/MailAuditor.php`
- `src/framework/Libs/MailBase.php`
- `src/framework/Libs/MailFromRemoteWP.php`
- `src/framework/Libs/SendinBlue.php`

### platform-utils — 27 archivos

Move generic optional process/time/helpers; replace minimal required runtime calls with PHP native logic..

- `src/framework/Exceptions/NotImplementedException.php`
- `src/framework/Helpers/date.php`
- `src/framework/Helpers/env.php`
- `src/framework/Helpers/etc.php`
- `src/framework/Helpers/loggers.php`
- `src/framework/Helpers/options.php`
- `src/framework/Helpers/url.php`
- `src/framework/Libs/ApacheWebServer.php`
- `src/framework/Libs/Bytes.php`
- `src/framework/Libs/Date.php`
- `src/framework/Libs/Env.php`
- `src/framework/Libs/Hardware.php`
- `src/framework/Libs/Messurements.php`
- `src/framework/Libs/Num.php`
- `src/framework/Libs/Options.php`
- `src/framework/Libs/Parallex.php`
- `src/framework/Libs/RandomGenerator.php`
- `src/framework/Libs/SortedIterator.php`
- `src/framework/Libs/System.php`
- `src/framework/Libs/TemporaryExceptionHandler.php`
- `src/framework/Libs/Time.php`
- `src/framework/Libs/TimeZone.php`
- `src/framework/Libs/Utils.php`
- `src/framework/Traits/ErrorReporting.php`
- `src/framework/Traits/ExceptionHandler.php`
- `src/framework/Traits/TimeExecutionTrait.php`
- `src/framework/Traits/Uuids.php`

### psr7 — 5 archivos

Move PSR-7 adapters and psr/http-message dependency to optional bridge..

- `src/framework/Helpers/psr7.php`
- `src/framework/Psr7/ResponseAdapter.php`
- `src/framework/Psr7/ServerRequestAdapter.php`
- `src/framework/Psr7/StreamAdapter.php`
- `src/framework/Psr7/UriAdapter.php`

### validation — 5 archivos

Move validation implementation/schema to validation extension..

- `src/framework/Exceptions/InvalidValidationException.php`
- `src/framework/Interfaces/ITransformer.php`
- `src/framework/Interfaces/IValidator.php`
- `src/framework/Libs/ValidationRules.php`
- `src/framework/Libs/Validator.php`

### views-html — 14 archivos

Move templating, HTML, CSS and response view builders outside JSON-first kernel..

- `src/framework/Controllers/WebController.php`
- `src/framework/Helpers/html.php`
- `src/framework/Helpers/view.php`
- `src/framework/Libs/CSS.php`
- `src/framework/Libs/CustomTags.php`
- `src/framework/Libs/EasyHTMLTable.php`
- `src/framework/Libs/Fragment.php`
- `src/framework/Libs/HTML.php`
- `src/framework/Libs/JsonLd.php`
- `src/framework/Libs/SiteMap.php`
- `src/framework/Libs/ViewModel.php`
- `src/framework/Libs/XML.php`
- `src/framework/Traits/PagesTrait.php`
- `src/framework/View.php`

### webhooks — 7 archivos

Move webhook publishing/delivery to webhook extension..

- `src/framework/Interfaces/IWebhookTransport.php`
- `src/framework/Libs/WebhookDeliveryDispatcher.php`
- `src/framework/Libs/WebhookEndpointPolicy.php`
- `src/framework/Libs/WebhookEvent.php`
- `src/framework/Libs/WebhookHttpTransport.php`
- `src/framework/Libs/WebhookPublisher.php`
- `src/framework/Libs/WebhookSubscriptionMatcher.php`

## Estado de implementación en master — 2026-10-23

- `src/framework/` contiene 14 clases genéricas para HTTP, routing, middleware, DI, errores y configuración en memoria. Se retiraron de ese árbol 239 archivos candidatos y se conservaron en `archive/simplerest-framework-extractions-2026-10-23/`; Composer excluye ese archivo de sus artefactos y no lo autoload.
- Se eliminaron los 17 archivos clasificados para borrar, incluidos el cargador de aliases y los shims. `composer.json` requiere únicamente PHP, no define `suggest`, `require-dev`, `autoload-dev` ni `autoload.files`; la configuración PHPUnit del monorepo vive en `composer-dev.json`.
- `Request`, `Response`, `WebRouter`, `FrontController`, `MiddlewareHandler` y `ErrorHandler` se redujeron a un flujo HTTP genérico. Una ruta cuyo middleware no puede resolverse responde 503 antes de ejecutar el handler.
- La prueba semántica de Graphify no pudo ejecutarse porque el servicio rechazó la credencial con HTTP 401; el mapa AST obtenido es auxiliar y presenta endpoints sin resolver. La revisión completa de consumidores y la migración de los paquetes existentes siguen pendientes.
- Las páginas canónicas de arquitectura, API, base de datos, seguridad, CLI e integraciones todavía describen capacidades del framework anterior. Se clasifican como documentación pendiente de reconciliación con el nuevo contrato; no se reescribieron en esta implementación.
- El core 3.x no tiene tag ni publicación en Packagist. Las etiquetas actuales de extensiones no se modificaron y no se afirma compatibilidad con este microcore.
