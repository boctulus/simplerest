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
| SimpleRest | 1795e8925 (master, igual a origin/master) | 272 |
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

Los dos commits se enviaron a origin/master. No se modificaron manifiestos, tags ni el directorio simplerest-pack.

## Decisiones comprobadas y diferencias controladas

| Ruta | Clase | Decisión actual | Prueba / estado |
| --- | --- | --- | --- |
| Traits/QueryBuilderTrait.php | A | Portada. Queda una diferencia textual frente a GoProp, sin diferencia observada en el contrato de ordenamiento. | 6 pruebas focalizadas aprobadas. |
| Request.php | A | Portadas las mejoras de lectura de cabeceras y MIME; se retienen los métodos inmutables que SimpleRest ya contiene. | 3 pruebas y smoke HTTP aprobados. |
| Libs/Url.php | A | Portado el error tipado. Url::getAppUrl() de GoProp sigue pendiente: comprobar consumidores, fuente de app_url y uso en CLI antes de decidir. | JSON inválido probado; getAppUrl() no probado ni portado. |
| Exceptions/InvalidRequestBodyException.php | A | La excepción genérica ya existe en SimpleRest; se omitió la referencia documental específica de GoProp. | Estado HTTP 400 probado. |
| Model.php | B | Se conserva el código más amplio de SimpleRest (ModelRecord y helpers de registros); no se reemplaza por la versión reducida de GoProp. | Comparación de fuente; pruebas de persistencia no ejecutadas. |
| Libs/DBRels.php | E | El diff revisado solo elimina imports y corrige un comentario. No hay mejora funcional que portar. | Sin prueba necesaria para esta diferencia textual. |
| Schema.php | B / convergida | No aparece entre las rutas divergentes actuales. La rama PostgreSQL de hasTable() ya está en ambos blobs versionados. | La consulta de metadatos no se ejecutó contra una base de datos en esta fase. |
| FrontController.php | D | Pendiente acordar y probar el contrato 401 frente a 403 por estado de autenticación y permiso. No se cambia semántica de seguridad sin esa prueba. | Pendiente. |
| Libs/CommandRegistry.php | D | Pendiente implementar precedencia y diagnóstico de colisiones sobre la infraestructura actual. | Pendiente; no se ha migrado GoProp. |
| Libs/CorsHandler.php | D | GoProp no usa Referer como sustituto de Origin; revisar con pruebas antes de cambiar comportamiento CORS. | Pendiente. |
| Response.php | D | El metadato hermano de data es una mejora reusable; los cambios de GoProp a status-lines y la auditoría de impersonación no se copiarán sin evaluación. | Pendiente de prueba de envelope y contrato HTTP. |
| Validator.php | A candidata | GoProp añade skipBoundsChecks() para filtros de lectura. Falta portar el contrato junto con su llamada en ApiController y probar que conserva la validación de tipo. | Pendiente. |
| Libs/Files.php | A candidata | GoProp conserva la ruta original cuando realpath() devuelve false, evitando un mensaje de error vacío. | Pendiente de prueba. |

Las demás rutas comunes con blob distinto permanecen sin clasificación final. Pendiente significa que no se ha elegido todavía entre A–E; no es una decisión de conservar ni de portar.

| Ruta | Estado |
| --- | --- |
| Api/ApiController.php, Api/AuthController.php, Api/Collections.php | Pendiente de revisión de API, ACL, autenticación y configuración de aplicación. |
| Handlers/ApiHandler.php, Handlers/ErrorHandler.php | Pendiente de trazar resolución, errores y tests existentes. |
| Helpers/env.php, Helpers/loggers.php, Helpers/view.php | Pendiente de comparar efectos genéricos y dependencias del host. |
| Interfaces/ISubResources.php | Pendiente de comparar contrato y consumidores. |
| Libs/Env.php, Libs/FileUploader.php, Libs/GoogleDrive.php, Libs/i18n/Translate.php, Libs/LangDetector.php, Libs/Mail.php, Libs/MailAuditor.php, Libs/PostmanGenerator.php, Libs/System.php | Pendiente de revisar comportamiento, dependencias opcionales y pruebas. |
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
- php -l en todos los PHP modificados: sin errores.
- Servidor PHP built-in local: POST de JSON inválido con charset=utf-8 → HTTP 400 y respuesta JSON genérica.
- No se ejecutó la suite completa ni pruebas que conecten a MySQL/PostgreSQL; no se configuró ni se modificó ninguna base de datos.
- La compatibilidad de GoProp como consumidor Composer aún no está demostrada. No se modificó su composer.json ni se cambió de rama.

## Siguiente trabajo de Fase 1

Continuar con los cambios genéricos priorizados —en especial Validator/filtros, metadatos de Response, CORS, Files, auth/ACL, mail y localización— con cambios pequeños y pruebas seguras. Después cerrar la clasificación de las 33 diferencias y las clases exclusivas. No pasar a Fase 2 hasta que ese gate esté completo.
