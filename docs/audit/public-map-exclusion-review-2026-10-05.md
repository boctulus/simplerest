# Auditoría semántica de exclusiones del mapa público

Fecha: 2026-10-05

## Alcance y criterio

Revisé los 18 Markdown que permanecen bajo docs/framework, los dos documentos que el inventario anterior marcaba needs-review, la diferencia entre los conteos 99/98 y las páginas fuera del mapa con material técnico que podría alimentar documentación pública. Contrasté las páginas candidatas con docs/index.md, el manifest y los hallazgos ya registrados en este README.

La ruta por sí sola no determina la clasificación. El inventario agrupa internal/audit en un solo valor; en la tabla siguiente separo internal de audit según el propósito de cada archivo. El inventario ahora distingue planes y registros de auditoría, material histórico que no es contrato vigente y guías con contenido aprovechable cuyas afirmaciones aún requieren revisión. No cambié afirmaciones ni ejemplos técnicos.

## docs/framework/

Ninguno de estos archivos es una guía pública canónica. La clasificación final de cada uno es:

| Markdown | Clasificación | Motivo semántico |
| --- | --- | --- |
| docs/framework/Index.md | internal | Es un puntero de compatibilidad: remite al único mapa vigente y explica dónde quedaron los archivos históricos. No desarrolla una capacidad del framework. |
| docs/framework/_internal/issues/migration_table_name_error.md | audit | Registro de un error de migración, su causa observada y su workaround. Es historial de ingeniería, no instrucción vigente para usuarios. |
| docs/framework/_internal/to-do/api-rest-from-frontcontroller-to-webrouter.md | internal | Propone una refactorización y compara alternativas; sus descripciones de routers son premisas de un plan, no un contrato actual. |
| docs/framework/_internal/to-do/comparativa_simplerest_vs_laravel.md | internal | Compara Artisan y Laravel y propone extensiones hipotéticas para SimpleRest. No describe comandos actuales de SimpleRest. |
| docs/framework/_internal/to-do/fix-perfomance.md | internal | Lista mejoras deseadas sobre routers, ACL y módulos; no contiene pasos de uso ni comportamiento vigente. |
| docs/framework/_internal/to-do/in-progress/api-tests-progress.md | audit | Parte de seguimiento de archivos y resultados de pruebas. Es una instantánea de trabajo, no una referencia estable. |
| docs/framework/_internal/to-do/orm/ORM-Laravel-like.md | needs-review | Contiene instrucciones extensas de modelos, métodos estáticos y CRUD que podrían ser útiles. Sus afirmaciones requieren cotejo con Model, CLI y pruebas; además, el material ORM-STATUS conservado en auditoría afirma lo contrario sobre el estado funcional del ORM. |
| docs/framework/_internal/to-do/orm/ORM.md | needs-review | Es un tutorial de ORM, CRUD, relaciones y transacciones, no una mera nota de backlog. No debe publicarse hasta resolver sus afirmaciones frente al estado ORM contradictorio y al comportamiento actual. |
| docs/framework/_internal/to-do/phase-3-psr17-factories.md | internal | Plan de una fase PSR-17; lo presenta como trabajo planeado, no como API disponible. |
| docs/framework/_internal/to-do/phase-4-psr15-middleware.md | internal | Plan de una fase PSR-15; describe una implementación futura. |
| docs/framework/_internal/to-do/phase-5-stream-interface.md | internal | Plan no iniciado para reemplazar bodies por StreamInterface; sus ejemplos describen diseño futuro. |
| docs/framework/_internal/to-do/sections/Modules.md | needs-review | Tiene una guía concreta de creación, estructura, opciones y routing de módulos. Es candidata pública, pero hay que verificar el comando, las rutas y las opciones contra la implementación actual. |
| docs/framework/_internal/to-do/sections/Packages.md | needs-review | Tiene una guía de creación, estructura, Composer y migraciones de packages. Las dos copias conservadas discrepan en el comando de migraciones, así que requiere cotejo antes de publicación. |
| docs/framework/_internal/to-do/SimpleRest_Plan_de_Trabajo.md | internal | Plan de reorganización con una decisión explícita sobre no convertir módulos en packages. Es contexto de planificación, no manual de usuario. |
| docs/framework/_internal/to-do/TODO !must-do.md | internal | Roadmap de tareas futuras. Sus propuestas no son compromisos ni contratos de la versión actual. |
| docs/framework/_internal/to-do/TODO important.md | internal | Mezcla un estado general del framework con mejoras propuestas y afirmaciones sin trazabilidad por feature. Es material de planificación, no referencia vigente. |
| docs/framework/_internal/to-do/TODO ORM.md | internal | Discute una idea de ORM basado en arrays y escrituras bulk; explícitamente es una propuesta, no una guía de la API existente. |
| docs/framework/_internal/to-do/TODO RelationshipTrait.md | audit | Evaluación de implementación que identifica dependencias, side effects y falta de pruebas. Es una nota de revisión técnica. |

Las cuatro páginas de uso ORM/Modules/Packages están marcadas needs-review en el inventario, junto con sus copias docs/_internal/to-do/orm/ORM-Laravel-like.md, docs/_internal/to-do/orm/ORM.md, docs/_internal/to-do/sections/Modules.md y docs/_internal/to-do/sections/Packages.md. Así no quedan descartadas por sus directorios, pero tampoco se presentan como actuales antes de verificarlas.

## Los dos needs-review anteriores

| Documento | Contenido y resolución | Clasificación final |
| --- | --- | --- |
| docs/README.md | Es la portada del repositorio para quien entra por GitHub. Remite a docs/index.md y repite política y navegación; no es una segunda página del sitio. Conservar como entrada del repositorio y excluir del build público. | internal |
| docs/security/README.md | Es un índice en inglés que remite a las dos páginas de seguridad canónicas y al ledger de auditoría; también declara el alcance y límite de la auditoría. El mapa ya enlaza directamente las páginas canónicas. Conservar como referencia de auditoría, no como página pública independiente. | audit |

No queda una decisión pendiente para esos dos archivos. Si se desea una página pública de aterrizaje para seguridad, debe derivarse de las fuentes canónicas y no publicar el ledger interno.

## Por qué hay 99 documentos legacy y 98 movimientos

El snapshot pre-movimiento registraba 98 legacy entre 192 Markdown. En el lote final se separaron 98 páginas históricas. La página que completa los 99 del inventario final es docs/_internal/legacy/INDEX-2026-05.md: ya existía en esa carpeta como copia histórica del índice antiguo y el snapshot previo la clasificaba internal/audit. La clasificación final la reconoce como legacy; no fue una de las 98 páginas movidas en el lote final. Por tanto, 99 = 98 páginas separadas en ese lote + 1 índice histórico preexistente.

No falta una página en el conteo ni se perdió una ruta por la diferencia.

La verificación de rutas detectó y corrigió otra discrepancia del inventario: la tarjeta docs/to-do/in-progress/webhooks-scope-isolation.md ya había salido de docs/to-do/webhooks-scope-isolation.md, pero la ruta anterior seguía en el JSON. El snapshot pre-movimiento conserva la ruta antigua como evidencia histórica; el inventario actual coincide con el árbol actual.

## Páginas fuera del mapa con material potencialmente útil

“Potencialmente útil” significa que el texto puede servir como fuente de trabajo, no que sea correcto para publicación. Las siguientes páginas no son actuales sólo por existir. Se conservaron como legacy o audit porque contienen afirmaciones antiguas, contradictorias, no reproducidas o todavía sin auditoría de claims.

| Área pública | Páginas excluidas que vale la pena revisar | Motivo de exclusión actual |
| --- | --- | --- |
| Getting Started | docs/_internal/legacy/QuickStart.md; docs/audit/pending/framework/QuickStart.md | La auditoría existente rechaza que make schema cree tablas o deje CRUD listo y rechaza la ruta /api/products sin versión. No se reprodujo una instalación limpia de principio a fin. |
| CRUD y API | docs/_internal/legacy/AutomaticEndpoints-Summary.md; docs/audit/pending/framework/AutomaticEndpoints-Summary.md; docs/_internal/legacy/SimpleRest-API-Rest.md; docs/_internal/legacy/framework/SimpleRest-API-Rest.md; docs/_internal/legacy/SimpleRest-Complete-Docs.md; docs/audit/pending/framework/SimpleRest-Complete-Docs.md; docs/_internal/legacy/SubResources.md; docs/audit/pending/framework/SubResources.md; docs/_internal/legacy/Validation.md; docs/_internal/legacy/framework/Validation.md | El audit ya trazó partes del resolver, pero no un flujo completo de generación a CRUD. La escritura anidada descrita por una página legacy no está implementada según el claim auditado; el comportamiento de subrecursos y validación sigue parcial o pendiente. Los dos compendios completos son fuentes históricas amplias, no contratos actuales. |
| Database | docs/_internal/legacy/QueryBuilder.md; docs/audit/pending/framework/QueryBuilder.md; docs/_internal/legacy/Schemas.md; docs/audit/pending/framework/Schemas.md; docs/_internal/legacy/AutoJoins.md; docs/audit/pending/framework/AutoJoins.md; docs/_internal/legacy/Multi-Tenant.md; docs/_internal/legacy/framework/Multi-Tenant.md; docs/audit/pending/framework/Multi-Tenant.md; docs/_internal/legacy/framework/PGSQL-Compatibility.md; docs/audit/pending/framework/PGSQL-Compatibility.md; docs/audit/pending/framework/PGSQL-Known-Issues.md | Las páginas canónicas actuales cubren conexiones, lectura del Query Builder y descriptores de schema con límites expresos. El audit rechaza o deja pendientes varias afirmaciones legacy sobre CRUD, DDL, relaciones, transacciones y compatibilidad por motor. |
| CLI | docs/_internal/legacy/CommandLine.md; docs/_internal/legacy/framework/CommandLine.md; docs/_internal/legacy/commands/AclCommands.md; docs/_internal/legacy/framework/commands/AclCommands.md; docs/_internal/legacy/commands/PackCommand.md; docs/_internal/legacy/framework/commands/PackCommand.md | Son guías de comandos y páginas de comandos concretos, pero el inventario de CLI y los ejemplos reproducidos siguen pendientes. Se mantienen como fuentes legacy para esa fase. |
| Deployment | docs/_internal/legacy/Deployment.md; docs/_internal/legacy/framework/Deployment.md | Son guías antiguas; la página canónica actual declara que aún no hay instrucciones de deployment validadas. No hay una ejecución de despliegue que respalde estos pasos. |
| Routing y ciclo de petición | docs/_internal/legacy/Framework-Architecture.md; docs/audit/pending/framework/Framework-Architecture.md; docs/_internal/legacy/WebRouter.md; docs/audit/pending/framework/WebRouter.md; docs/_internal/legacy/Routing.md; docs/audit/pending/framework/Routing.md; docs/_internal/legacy/FrontController.md; docs/audit/pending/framework/FrontController.md; docs/_internal/legacy/Request.md; docs/audit/pending/framework/Request.md; docs/_internal/legacy/Response.md; docs/audit/pending/framework/Response.md | El mapa ya contiene arquitectura y ciclo de petición con alcance acotado. Las guías antiguas mezclan comportamiento verificado con APIs, sintaxis y flujos pendientes; el audit conserva claim por claim lo que no se promovió. |
| Módulos, packages y ORM | docs/_internal/legacy/Packages and Modules.md; docs/_internal/legacy/framework/Packages and Modules.md; docs/framework/_internal/to-do/orm/ORM-Laravel-like.md; docs/framework/_internal/to-do/orm/ORM.md; docs/framework/_internal/to-do/sections/Modules.md; docs/framework/_internal/to-do/sections/Packages.md; docs/_internal/to-do/orm/ORM-Laravel-like.md; docs/_internal/to-do/orm/ORM.md; docs/_internal/to-do/sections/Modules.md; docs/_internal/to-do/sections/Packages.md | Contienen instrucciones potencialmente publicables, pero hay duplicados, diferencias entre copias y afirmaciones ORM contradictorias. Quedan visibles como revisión pendiente, sin incorporarse como contrato. |
| Otras capacidades del framework | docs/_internal/legacy/ApiClient.md; docs/_internal/legacy/framework/ApiClient.md; docs/_internal/legacy/Caching.md; docs/_internal/legacy/framework/Caching.md; docs/_internal/legacy/EventBus.md; docs/_internal/legacy/framework/EventBus.md; docs/_internal/legacy/Exceptions.md; docs/_internal/legacy/framework/Exceptions.md; docs/_internal/legacy/Helpers.md; docs/_internal/legacy/framework/Helpers.md; docs/_internal/legacy/Middlewares.md; docs/_internal/legacy/framework/Middlewares.md; docs/_internal/legacy/ViewEngine.md; docs/_internal/legacy/framework/ViewEngine.md; docs/_internal/legacy/i18n.md; docs/_internal/legacy/framework/i18n.md; docs/_internal/legacy/Pacakges.md; docs/_internal/legacy/framework/Pacakges.md; docs/_internal/legacy/packager-implementation.md; docs/_internal/legacy/framework/Packager-implementation.md; docs/_internal/legacy/framework/PSR-7.md; docs/_internal/legacy/framework/PSR-SUMMARY.md; docs/_internal/legacy/Performance.md; docs/_internal/legacy/framework/Performance-Benchmark.md; docs/audit/pending/framework/SimpleRest-Philosophy.md | Son referencias técnicas o de uso, pero no están en el mapa y no se han revalidado aquí como documentación vigente. Deben evaluarse por feature si entran en el alcance público actual; las cifras de rendimiento requieren una medición reproducible. |
| Seguridad | docs/_internal/legacy/Authentication.md; docs/_internal/legacy/framework/Authentication.md; docs/_internal/legacy/ACL.md; docs/_internal/legacy/framework/ACL.md | Hay páginas canónicas actuales de autenticación y ACL en el mapa. Las copias antiguas no son una carencia pública; se conservan para historial y contraste. |
| Webhooks | docs/api/webhooks.md es la página canónica pública. docs/to-do/done/webhooks-callback-security.md, docs/to-do/done/webhooks-decouple-dispatcher.md, docs/to-do/webhooks-async-delivery-reliability.md y docs/to-do/in-progress/webhooks-scope-isolation.md son cuatro tarjetas internas de seguridad, diseño y trabajo. | El inventario incluye los cinco Markdown revisados en el lote final. Las tarjetas describen el trabajo del proyecto, no instrucciones para integrar o consumir la API. |

### Resultado por gap público

- Getting Started, CLI y Deployment tienen una página canónica de estado en el mapa, pero siguen declarando explícitamente que faltan una instalación limpia reproducida, el inventario de comandos y las instrucciones de deployment validadas. Los manuales legacy son material de entrada para completar esas páginas, no sustitutos publicables.
- CRUD y Database sí tienen referencias canónicas actuales; la auditoría previa documenta qué partes son source-traced y qué claims de los manuales antiguos fueron rechazados, parciales o no reproducidos.
- Webhooks ya tiene página canónica pública. Sus cuatro tarjetas están clasificadas por su función de trabajo, no por la carpeta donde viven.
- Modules, Packages y ORM eran las omisiones semánticas más claras: el inventario previo los había llamado internal/audit sólo por su ruta, aunque contienen guías de uso. Ahora figuran como needs-review, visibles para una futura verificación.

Por tanto, hay exclusiones justificadas por contenido histórico, auditoría, propuesta o falta de evidencia; también había guías útiles mal descritas como internas por su ubicación. La corrección de esas clasificaciones no añade contenido técnico ni incorpora páginas al mapa público.

## Inventario después de la auditoría

El inventario actualizado registra 198 Markdown: 19 canonical, 99 legacy, 72 internal/audit y 8 needs-review. Se agregó este informe como internal/audit y se cambió la clasificación de dos páginas secundarias y ocho copias de guías candidatas. El manifest y las 19 fuentes canónicas no cambiaron.

No se modificó contenido técnico. Los cambios se limitan a este informe, su índice interno y metadatos de clasificación/conteos del inventario.
