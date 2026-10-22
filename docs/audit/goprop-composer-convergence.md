# Convergencia del core SimpleRest–GoProp

**Estado:** Fase 1 en curso. La modularización Composer y la migración de GoProp todavía no han comenzado.
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

## Rebaseline de los árboles versionados

La comparación usa blobs Git del HEAD de cada repositorio, no hashes de archivos de trabajo que podrían diferir por finales de línea.

| Árbol | Ref corta | Archivos src/framework/ |
| --- | --- | ---: |
| SimpleRest | ce36c0ca0 (master, igual a origin/master) | 272 |
| GoProp | f83e7949 (main) | 274 |
| Rutas comunes | — | 262 |
| Comunes con blob distinto | — | 33 |
| Exclusivas de GoProp | — | 12 |
| Exclusivas de SimpleRest | — | 10 |

No hay cambios sin commit bajo src/framework/ en ninguno de los dos repositorios en este corte. SimpleRest conserva el cambio local preexistente en app/Views/templates/adminlte_tpl.php; no forma parte de estos commits. GoProp main permanece intacta.

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

Los commits funcionales se enviaron a origin/master. No se modificaron manifiestos, tags ni el directorio simplerest-pack.

## Decisiones comprobadas y diferencias controladas

| Ruta | Clase | Decisión actual | Prueba / estado |
| --- | --- | --- | --- |
| Traits/QueryBuilderTrait.php | A | Portada. Queda una diferencia textual frente a GoProp, sin diferencia observada en el contrato de ordenamiento. | 6 pruebas focalizadas aprobadas. |
| Request.php | A | Portadas las mejoras de lectura de cabeceras y MIME; se retienen los métodos inmutables que SimpleRest ya contiene. | 3 pruebas y smoke HTTP aprobados. |
| Libs/Url.php | A | Portados el error tipado de JSON y getAppUrl(), que usa app_url para enlaces públicos en peticiones y CLI. | JSON inválido probado en smoke HTTP; UrlAppUrlTest.php: 2/2 pruebas. |
| Exceptions/InvalidRequestBodyException.php | A | La excepción genérica ya existe en SimpleRest; se omitió la referencia documental específica de GoProp. | Estado HTTP 400 probado. |
| Model.php | B | Se conserva el código más amplio de SimpleRest (ModelRecord y helpers de registros); no se reemplaza por la versión reducida de GoProp. | Comparación de fuente; pruebas de persistencia no ejecutadas. |
| Libs/DBRels.php | E | El diff revisado solo elimina imports y corrige un comentario. No hay mejora funcional que portar. | Sin prueba necesaria para esta diferencia textual. |
| Schema.php | B / convergida | No aparece entre las rutas divergentes actuales. La rama PostgreSQL de hasTable() ya está en ambos blobs versionados. | La consulta de metadatos no se ejecutó contra una base de datos en esta fase. |
| FrontController.php | D | Pendiente acordar y probar el contrato 401 frente a 403 por estado de autenticación y permiso. No se cambia semántica de seguridad sin esa prueba. | Pendiente. |
| Libs/CommandRegistry.php | A / D | A — Portada la ayuda de grupo cuando no hay subcomando, evitando ejecución implícita del primer comando. D — Falta resolver precedencia aplicación/extensiones/framework, colisiones y aliases; además, buildSearchPaths() no descubre comandos bajo Composer vendor/. | CommandRegistryDispatchTest.php: 2/2 pruebas, 11 aserciones; resolución Composer y colisiones pendientes para Fase 2. |
| Libs/CorsHandler.php | D | GoProp no usa Referer como sustituto de Origin; revisar con pruebas antes de cambiar comportamiento CORS. | Pendiente. |
| Response.php | A / B / C | A — Conserva metadatos hermanos y regenera los campos reservados del envelope. B — Mantiene el emisor de status HTTP de SimpleRest. C — No incorpora la auditoría de mutaciones de impersonación, que depende de GoProp. | unit-tests/http/ResponseEnvelopeMetadataTest.php: 3/3 pruebas, 10 aserciones; no se ejecutaron rutas ni conexiones. |
| Validator.php | A | Portado skipBoundsChecks() para que los filtros de lectura omitan solo los límites de escritura y mantengan la validación de tipo. | 2 pruebas focalizadas aprobadas. |
| Libs/Files.php | A | Se conserva la ruta solicitada en el mensaje cuando realpath() devuelve false. | FilesWritablePathTest.php: 1/1 prueba, 3 aserciones en Linux/WSL. |
| Libs/LangDetector.php | A | Se incorporan detección por idioma y soporte genérico italiano, hindi y bengalí; se omiten palabras de dominio de GoProp. | LangDetectorTest.php: 4/4 pruebas, 9 aserciones en PHP 8.1 y 8.4. |

Las demás rutas comunes con blob distinto permanecen sin clasificación final. Pendiente significa que no se ha elegido todavía entre A–E; no es una decisión de conservar ni de portar.

| Ruta | Estado |
| --- | --- |
| Api/ApiController.php | Portada la llamada de filtros de lectura a skipBoundsChecks(); el resto de sus diferencias queda pendiente de revisión de API y ACL. |
| Api/AuthController.php, Api/Collections.php | Pendiente de revisión de autenticación, ACL y configuración de aplicación. |
| Handlers/ApiHandler.php, Handlers/ErrorHandler.php | Pendiente de trazar resolución, errores y tests existentes. |
| Helpers/env.php, Helpers/loggers.php, Helpers/view.php | Pendiente de comparar efectos genéricos y dependencias del host. |
| Interfaces/ISubResources.php | Pendiente de comparar contrato y consumidores. |
| Libs/Env.php, Libs/FileUploader.php, Libs/GoogleDrive.php, Libs/i18n/Translate.php, Libs/Mail.php, Libs/MailAuditor.php, Libs/PostmanGenerator.php, Libs/System.php | Pendiente de revisar comportamiento, dependencias opcionales y pruebas. |
| Security/Acl.php, Security/Compiler/EffectivePermissionCompiler.php | Pendiente de comparación semántica de ACL; no copiar cambios de permisos automáticamente. |
| View.php | Pendiente de trazar el contrato de renderizado y sus consumidores. |

## Clases exclusivas

**Solo en GoProp (12):**

- Las 11 clases de Libs/Impersonation/ parecen ligadas al producto y a sus límites de seguridad. Su propietario provisional es GoProp; no se trasladan al core genérico sin evidencia de reutilización y una revisión de sus controles de mutación.
- Libs/RedisLock.php queda pendiente de verificar por consumidor, cliente Redis requerido y estrategia de distribución. No se asume que deba entrar al core.

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
- php -l en todos los PHP modificados: sin errores.
- Servidor PHP built-in local: POST de JSON inválido con charset=utf-8 → HTTP 400 y respuesta JSON genérica.
- No se ejecutó la suite completa ni pruebas que conecten a MySQL/PostgreSQL; no se configuró ni se modificó ninguna base de datos.
- La compatibilidad de GoProp como consumidor Composer aún no está demostrada. No se modificó su composer.json ni se cambió de rama.

## Siguiente trabajo de Fase 1

Continuar con los cambios genéricos priorizados —CORS, auth/ACL, mail y localización— con cambios pequeños y pruebas seguras. Después cerrar la clasificación de las 33 diferencias y las clases exclusivas. No pasar a Fase 2 hasta que ese gate esté completo.
