# Auditoría: integración Composer de SimpleRest en GoProp

**Tipo:** ISSUE — auditoría puntual de repositorios y release.

**Fecha de corte:** 2026-10-22.

**Estado:** no apta para migrar directamente GoProp a la versión Composer publicada.

## Dictamen

Mantener por ahora el core cargado desde `src/framework/` de GoProp. La instalación directa de `boctulus/simplerest` no es resoluble con las restricciones actuales de Composer y, aun corregidas, no conserva por sí sola la disponibilidad de los comandos CLI de SimpleRest. El release publicado tampoco corresponde a la salida curada del empaquetador.

Antes de migrar hay que fijar una fuente de distribución reproducible, resolver compatibilidad PHP/dependencias, decidir qué diferencias del core deben subir a SimpleRest y definir cómo se registran comandos sin colisiones.

## Snapshots y método

| Objeto | Referencia usada | Alcance |
| --- | --- | --- |
| GoProp | `36f2822d2ca5fde3998d3f71dad82c66921e8ce1` | Manifiesto Composer y hashes del core versionado |
| SimpleRest de desarrollo | `54c9e0007aba5f738b02d80168617ed5e8ddae2f` (`master`) | Manifiesto, registry CLI, empaquetador y árbol versionado |
| SimpleRest publicado | Packagist `v1.0.3`, referencia `e686ceee139f69c444c45a3c4f941842440f8a4f` | Metadatos publicados y árbol recursivo del commit asociado |
| Etiqueta remota actual | `v1.0.3` apunta al commit `6d86ff75e1aa0a312af853b0c776667cffc24d0a` | Comparación de referencia con el commit que Packagist muestra para esa versión |

La comparación del core usa hashes de Git para archivos en rutas comunes; una diferencia de hash no demuestra que el cambio sea una mejora. Los tamaños de la release publicada son sumas de bytes de los blobs del árbol Git, no el tamaño comprimido de su ZIP.

El árbol de trabajo de SimpleRest ya tenía cambios locales en `app/Views/templates/adminlte_tpl.php` y `src/framework/Libs/Schema.php`. No se incluyeron en las comparaciones de commits y se preservaron.

## 1. Fuente real de Composer y compatibilidad

La ficha de [Packagist para `boctulus/simplerest`](https://packagist.org/packages/boctulus/simplerest) identifica la versión estable `v1.0.3`, publicada el 2026-09-23 desde el repositorio GitHub `boctulus/simplerest`, con referencia `e686ceee139f69c444c45a3c4f941842440f8a4f`. `BuildCommand.php` usa como fuente por defecto `D:\laragon\www\simplerest`, como destino `D:\laragon\www\simplerest-pack` y expone `php com pack build`; esa salida no es la fuente que Packagist declara.

La etiqueta remota `v1.0.3` resuelve hoy a otro commit (`6d86ff7…`). La causa no está demostrada. Para reproducir lo que Packagist publicó hay que conservar la referencia `e686ce…` y revisar la historia de la etiqueta antes del siguiente release.

Los manifiestos de GoProp y del commit publicado tienen un bloqueo de resolución:

| Restricción | GoProp | SimpleRest `v1.0.3` | Resultado |
| --- | --- | --- | --- |
| PHP | `>=7.4,<8.4` | `>=8.1,<8.5` | Intersección `>=8.1,<8.4`; GoProp perdería compatibilidad declarada con PHP 7.4–8.0 |
| `phpmailer/phpmailer` | `^6.9` | `^7.1` | No existe una versión que satisfaga ambas restricciones |

GoProp bloquea PHPMailer `6.12.0` en su `composer.lock`. El manifiesto de desarrollo de SimpleRest y la versión que Packagist publica requieren `^7.1`. Por semver, las restricciones `^6.9` y `^7.1` son incompatibles; Composer no puede instalar ambos manifiestos sin cambiar uno de los contratos. El empaquetador elimina PHPMailer de `require` y lo convierte en `suggest`, pero esa transformación no demuestra qué publicó Packagist.

En GoProp, Composer todavía mapea `Boctulus\Simplerest\Core\` a su propio `src/framework/`; `composer.json` no requiere `boctulus/simplerest`. Por tanto, el core no está migrado a `vendor/`.

El checkout de SimpleRest no tiene un `.gitattributes` versionado. Composer documenta `export-ignore` ([guía oficial de distribución ligera](https://getcomposer.org/doc/02-libraries.md#light-weight-distribution-packages)) como el mecanismo para excluir archivos innecesarios del ZIP distribuido desde VCS. La presencia del empaquetador no reduce el artefacto de Packagist mientras el release siga apuntando al repositorio GitHub sin una exclusión equivalente.

## 2. Comandos CLI: propiedad, autoload y colisiones

El comportamiento de `autoload-dev` es root-only según el [schema oficial de Composer](https://getcomposer.org/doc/04-schema.md); que esos archivos existan en `vendor/` no los registra automáticamente en `CommandRegistry`.

`app/Commands/` es código de aplicación. GoProp conserva sus propios 233 archivos allí y su `composer.json` asigna ese directorio al namespace `Boctulus\Simplerest\Commands\`. Añadir una dependencia Composer la instala en `vendor/boctulus/simplerest`; no reemplaza físicamente `GoProp/app/Commands/`.

SimpleRest `v1.0.3` contiene 140 archivos bajo `app/Commands/`, pero su autoload de producción apunta al core (`src/framework/` y `src/`). El mapeo de `Boctulus\Simplerest\` a `app/` está bajo `autoload-dev`. Composer marca `autoload-dev` como **root-only**, por lo que no se incorpora al autoload de GoProp cuando SimpleRest es una dependencia. La inclusión física del archivo no equivale a disponibilidad funcional.

El `CommandRegistry` actual busca en:

1. `COMMANDS_PATH` de la aplicación;
2. las rutas declaradas en `config/commands.php`;
3. los paquetes explícitos bajo `PACKAGES_PATH/{vendor}/{package}/src/Commands`.

No busca automáticamente en `vendor/`. Al registrar una clave `grupo:comando`, asigna directamente a `$this->commands[$key]`; una definición encontrada después sustituye a la anterior sin error. Como los paquetes se recorren después del directorio local y de las rutas configuradas, el orden actual permite que un paquete opaque un comando de GoProp. `config/commands.php` ya conserva una nota sobre la colisión de Zippy con `php com users list`.

El `.packinclude` de SimpleRest conserva estos siete grupos: `file`, `migrations`, `module`, `mysql`, `router`, `sql` y `system`. No incluye, entre otros, `make` (43 archivos), `acl`, `users`, `roles`, `todo`, `skill`, `pack` o `doc`. Que un grupo esté incluido tampoco lo hace descubrible desde `vendor/`.

Además, `scripts/pack_framework.php` imprime que agregó `app/Commands` al autoload, pero el bloque correspondiente agrega explícitamente solo `app/Controllers`; no crea un mapeo PSR-4 para `app/Commands`.

**Criterio recomendado:** conservar `php com` y los comandos de GoProp como prioridad; distribuir grupos reutilizables de SimpleRest como paquetes Composer opcionales cuando proceda; registrar esos paquetes mediante rutas Composer explícitas; y fallar ante duplicados salvo override declarado. La precedencia objetivo sería GoProp → extensiones habilitadas → comandos estándar de SimpleRest.

## 3. Tamaño y contenido distribuido

El árbol Git del commit publicado `e686ce…` tiene 10.175 blobs y suma 669.324.750 bytes sin comprimir. Las áreas grandes incluyen contenido que una dependencia de framework normalmente no necesita:

| Ruta del release | Archivos | Bytes de blobs |
| --- | ---: | ---: |
| `docs/` | 231 | 67.058.909 |
| `app/` | 2.209 | 293.337.534 |
| `app/Modules/EatLeaf/assets/img/` | 34 | 281.966.518 |
| `packages/` | 742 | 1.721.521 |
| `unit-tests/` | 34 | 373.857 |
| `scripts/` | 53 | 252.540 |
| `src/framework/` | 261 | 1.529.268 |
| `examples/` | 1 | 793 |

**Conclusión de tamaño:** el release de Composer publicado todavía contiene la aplicación, documentación, tests, scripts, paquetes locales y assets voluminosos. No es el árbol reducido que el empaquetador intenta construir.

El `.packignore` del checkout de desarrollo excluye directorios amplios como `docs/`, `unit-tests/`, `examples/`, `app/`, `public/assets/`, `packages/` y migraciones; `.packinclude` vuelve a incluir algunos archivos y los siete grupos CLI indicados arriba. Esto prueba que hay una política de filtrado para la salida local, no que Packagist publique esa salida.

No se midió un ZIP nuevo de `simplerest-pack`: el destino existente está sucio. Tampoco se midió el tamaño comprimido del artefacto `v1.0.3`; solo la suma de blobs de su referencia publicada.

## 4. Paridad del core

Comparación completa por hashes entre GoProp `src/framework/` y el core del commit publicado `e686ce…`:

| Medida | Resultado |
| --- | ---: |
| Archivos del core de GoProp | 274 |
| Archivos del core publicado | 261 |
| Rutas comunes | 259 |
| Rutas comunes con hash distinto | 33 |
| Solo GoProp | 15 |
| Solo el release SimpleRest | 2 |

El `master` actual de SimpleRest todavía difiere de GoProp en 33 rutas comunes; hay 13 rutas solo en GoProp y 10 solo en SimpleRest. Frente al release `v1.0.3`, `Libs/DB.php` ya coincide en `master`, aunque no coincide en el commit publicado. Esto demuestra que el release publicado va por detrás del checkout de desarrollo en al menos una clase inspeccionada.

Rutas con hash distinto entre GoProp y el `master` actual de SimpleRest:

```text
Api/ApiController.php
Api/AuthController.php
Api/Collections.php
FrontController.php
Handlers/ApiHandler.php
Handlers/ErrorHandler.php
Helpers/env.php
Helpers/loggers.php
Helpers/view.php
Interfaces/ISubResources.php
Libs/CommandRegistry.php
Libs/CorsHandler.php
Libs/DBRels.php
Libs/Env.php
Libs/Files.php
Libs/FileUploader.php
Libs/GoogleDrive.php
Libs/i18n/Translate.php
Libs/LangDetector.php
Libs/Mail.php
Libs/MailAuditor.php
Libs/PostmanGenerator.php
Libs/Schema.php
Libs/System.php
Libs/Url.php
Libs/Validator.php
Model.php
Request.php
Response.php
Security/Acl.php
Security/Compiler/EffectivePermissionCompiler.php
Traits/QueryBuilderTrait.php
View.php
```

GoProp contiene rutas que no están en el release publicado: `Exceptions/InvalidRequestBodyException.php`; las once clases de `Libs/Impersonation/` (`ImpersonationActor`, `ImpersonationAuditService`, `ImpersonationDenialCode`, `ImpersonationDeniedException`, `ImpersonationManager`, `ImpersonationMode`, `ImpersonationMutationGuard`, `ImpersonationRequestContext`, `ImpersonationSessionService`, `ImpersonationStatus`, `ImpersonationTargetProviderInterface`); y `Libs/PdfBase.php`, `Libs/PHPParser.php`, `Libs/RedisLock.php`. No todas deben integrarse en el core: cada una necesita decisión de propiedad, dependencia opcional y destino de paquete.

| Clase | Comparación | Observación confirmada |
| --- | --- | --- |
| `Libs/DB.php` | Diferente en `v1.0.3`; igual en `master` | El release no incluye aún la versión actual del core en esta ruta |
| `Commands/BaseCommand.php` | Igual en `v1.0.3` | No hay divergencia de contenido en esta clase |
| `Libs/CommandRegistry.php` | Diferente | Hay que reconciliar descubrimiento y política de colisiones antes de Composer |
| `Libs/Schema.php` | Diferente | GoProp versionado tiene una rama PostgreSQL en `hasTable()` (`case 'pgsql'`) que falta en el `master` versionado de SimpleRest |
| `FrontController.php` | Diferente | GoProp responde 401 a este caso de API sin autenticar; SimpleRest responde 403. El contrato debe decidirse y probarse, no copiarse por hash |

El `Schema.php` local de SimpleRest, además, tiene cambios no confirmados; la comparación anterior usa solo commits y no los atribuye al core versionado. Ninguno de los 33 hashes distintos se clasifica automáticamente como mejora: falta revisión semántica y cobertura de pruebas por ruta.

## 5. Prioridades y salida propuesta

| Prioridad | Bloqueo | Criterio de cierre |
| --- | --- | --- |
| P0 | Referencia publicada y etiqueta `v1.0.3` no coinciden | Reproducir el contenido desde un commit/tag inmutable y verificar el artefacto recibido por un consumidor |
| P0 | PHPMailer `^6.9` frente a `^7.1`; además, intersección PHP más estrecha | Elegir y probar restricciones compatibles sin ampliar el requisito de GoProp por accidente |
| P0 | `app/Commands` no se descubre desde `vendor/` y las colisiones sobrescriben silenciosamente | Especificar registro Composer, precedencia y tratamiento explícito de duplicados; mantener los comandos propios de GoProp |
| P0 | Divergencia de core entre GoProp, `master` y release | Revisar los diffs semánticamente, trasladar al core canónico lo aprobado y probar las rutas afectadas |
| P1 | Release contiene 669 MB de blobs sin comprimir y contenido de aplicación/desarrollo | Publicar desde una fuente curada o filtrar el VCS dist; medir el ZIP real y auditar sus archivos |
| P1 | Comandos útiles mezclan tareas de framework y herramientas de desarrollo | Evaluar paquetes Composer opcionales para grupos reutilizables, sin empaquetar automáticamente el CLI de GoProp |

Secuencia recomendada, reversible:

1. Consolidar primero en SimpleRest las correcciones de core aprobadas, con pruebas para el contrato HTTP y PostgreSQL.
2. Definir una release reproducible de SimpleRest y resolver sus restricciones de PHP y dependencias.
3. Probar en un consumidor vacío de Composer la carga del core y el registro de los comandos incluidos, junto con una colisión deliberada.
4. Cambiar GoProp para consumir la release validada conservando `app/Commands/` y `php com`; retirar su copia `src/framework/` solo después de cerrar la paridad funcional.

## Límites de esta auditoría

- No se ejecutó `php com pack build`, `composer install`, pruebas, migraciones ni bootstrap HTTP.
- El empaquetador no se ejecutó: `SimpleRestPackager::run()` limpia el destino antes de validar el origen y el destino configurado por defecto es `D:\laragon\www\simplerest-pack`.
- La medida de blobs no sustituye la inspección del ZIP ni la instalación por Composer.
- El inventario de hashes mide igualdad de contenido, no intención, corrección ni riesgo de cada cambio.

El resultado responde si GoProp puede consumir hoy la dependencia publicada: **no con los manifiestos actuales**. La recomendación no autoriza una migración de código; establece los bloqueos y pruebas previas.
