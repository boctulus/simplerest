# Diferencias recientes del core SimpleRest ↔ GoProp

Auditoría estática realizada el 2026-10-26. El objetivo es detectar cambios recientes del core de GoProp que falten en SimpleRest y diferencias de dirección inversa que puedan afectar la paridad.

## Alcance y método

La ventana se seleccionó con `git log --since="7 days ago"` sobre `src/framework`, y se contrastaron los commits seleccionados y el diff neto de cada árbol. En SimpleRest se revisaron 51 commits desde el último estado anterior a la ventana (`54c9e0007aba5f738b02d80168617ed5e8ddae2f`, 2026-10-12) hasta `d88e483a691075670cdf80d839f106d220bb43e3`. En GoProp se revisaron 3 commits desde `cff7843baff6eb034d81b5619af75422d47b8221` (2026-10-19) hasta `01c7aad140835285392e7f2f952485586f004f23`.

El diff neto cubre 98 archivos de `src/framework` en SimpleRest y 17 en GoProp. El número de archivos de SimpleRest incluye extracciones de paquetes y ciclos de retirada/restauración del microcore; por eso también se inspeccionaron los destinos actuales en `packages/`. No se interpretó cada archivo retirado de `src/framework` como pérdida de funcionalidad.

## Cambios recientes de GoProp que SimpleRest debía reflejar

| Commit GoProp | Clase o grupo | Resultado en SimpleRest |
|---|---|---|
| `b39c7cde` | `ApiController`: reemplaza `get_fks()` por `DBRels::getFKs()` | Reflejado: el controlador de SimpleRest usa `DBRels::getFKs()` y el método existe en `DBRels`. |
| `4b3eb46a` | `Security\Acl`: resuelve `acl_file` bajo `SECURITY_PATH` | Reflejado: SimpleRest valida `SECURITY_PATH . Config::get()['acl_file']`. |
| `eb0c95f1` | `Response`: rollback y cierre de conexiones al destruir la respuesta | Reflejado: SimpleRest llama a `DB::rollbackAndCloseAllConnections()` y captura errores de limpieza; `DB` implementa esa operación. |
| `b39c7cde` | Elimina `CMS_Scanner` del core | Reubicado en `packages/boctulus/simplerest-web-inspection`, con alias de compatibilidad. |
| `b39c7cde` | Elimina `HtmlBuilder` del core | Reubicado en `packages/boctulus/simplerest-bootstrap`; el Composer package se llama `boctulus/simplerest-bootstrap-html` y conserva alias legacy. |
| `b39c7cde` | Elimina `AngularCleaner` y `BootstrapCleaner` | Ya no están en el core de ninguno de los dos árboles. En el checkout actual tampoco encontré su implementación bajo `packages/`; la funcionalidad/distribución del paquete opcional de herramientas queda por confirmar. |
| `b39c7cde` | Cambia `ImpersonationManager` para permitir motivo de cambio de modo vacío | No portado como cambio aislado: SimpleRest no contiene esa clase ni `ImpersonationRequestContext`, aunque su `ApiController` importa ambas y llama a sus métodos incondicionalmente. El autoload actual devuelve `class_exists() === false` para las dos; los únicos stubs encontrados están en fixtures de pruebas. |

En los cambios de FK, ACL y limpieza de transacciones no queda una corrección reciente de GoProp pendiente de trasladar a SimpleRest. Las retiradas de CMS Scanner y HTML Builder están cubiertas por paquetes locales; la disponibilidad de los cleaners fuera del core no quedó acreditada.

## Diferencias de `QueryBuilderTrait`

La corrección de SimpleRest `d88e483a6` no aparece en GoProp y debe trasladarse al repositorio canónico de SimpleRest/GoProp antes de depender de `whereColumn()` con columnas calificadas:

- SimpleRest valida identificadores con segmentos separados por puntos y agrega cada comparación a `where[]` con `AND`.
- GoProp todavía valida con `alpha_num_dash`, que rechaza puntos, y guarda la comparación en un único `$where_raw_q`. Así, una columna como `co_owners.organization_id` falla en la validación; además, una segunda llamada a `whereColumn()` sustituye la primera.
- `orderBy()` sí tiene la validación de direcciones y formas de entrada presente en ambos árboles; la diferencia observada es de formato, no de comportamiento.
- SimpleRest movió cuatro constantes del trait a `Model` en `fa54c6a39` para mantener PHP 8.1. GoProp conserva constantes en `QueryBuilderTrait`, pese a declarar `php >=8.1,<8.4`; es una diferencia de compatibilidad en dirección GoProp → SimpleRest que conviene corregir también en GoProp.

## Otros cambios del core SimpleRest en la ventana

El diff de todo `src/framework` incluye cambios que no son traslados pendientes desde los tres commits recientes de GoProp. Los grupos más relevantes son:

- **Configuración y comandos:** `Env`/`System` incorporan capas de `.env` y `.dev-env`, prioridad del entorno de proceso, validación de claves requeridas y propagación del error de inicialización; `CommandRegistry` descubre comandos planos de paquetes.
- **API y HTTP:** `ApiController`, `Collections`, `Request`, `Response`, `ApiHandler`, `ErrorHandler` y `RequestHandler` reciben validación de escrituras anidadas, autenticación/autorización, cuerpos JSON, códigos de respuesta y metadatos del envelope.
- **Persistencia y validación:** `DB`, `DBRels`, `Schema`, `FileUploader`, `Validator` y `QueryBuilderTrait` reciben cambios de estado transaccional por conexión, protección al cerrar transacciones activas, validación de archivos y restricciones de esquema/consulta.
- **Relaciones y compatibilidad:** `SubResourceHandler` corrige alias plurales de pivote; se ajustan namespaces y compatibilidad de clases mientras mail, CSS, minificación y otras integraciones salen del core hacia paquetes.

Estos cambios describen el trabajo reciente de SimpleRest; esta auditoría no afirma equivalencia funcional línea por línea con cada versión histórica de GoProp para las 98 rutas netas.

## Hallazgos y acciones

1. **P0 — GoProp:** trasladar y probar la corrección de `whereColumn()` de SimpleRest; incluir columnas calificadas y dos comparaciones consecutivas.
2. **P0 — SimpleRest:** resolver las referencias sin implementación a `ImpersonationManager` y `ImpersonationRequestContext` en `ApiController`. La búsqueda/autoload estático confirma que faltan en este checkout, pero no se ejecutó una solicitud HTTP real.
3. **P1 — GoProp:** mover las constantes de `QueryBuilderTrait` a una clase compatible con PHP 8.1, como ya hizo SimpleRest.
4. **P1 — distribución:** confirmar dónde se publican `AngularCleaner` y `BootstrapCleaner`; no están presentes en el core ni bajo `packages/` de este checkout.

## Límites de validación

Se inspeccionaron historial, diffs, destinos de paquetes y autoload de las clases de impersonación. No se ejecutaron suites HTTP, pruebas con base de datos ni validación de publicación/instalación de todos los paquetes. Este informe no demuestra paridad de ejecución entre GoProp y SimpleRest.
