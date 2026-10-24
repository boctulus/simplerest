# Rendimiento y benchmarks

Esta guía describe las herramientas de medición presentes en el checkout y sus límites. No declara una cifra vigente de rendimiento ni compara SimpleRest con otro framework. Los scripts no se ejecutaron durante esta auditoría porque varios pueden escribir en la base de datos o llamar endpoints mutables.

## Herramientas que existen

| Archivo | Medición que intenta realizar | Límite o efecto observado en el código |
| --- | --- | --- |
| `scripts/benchmarks/00_bootstrap.php` | Bootstrap de `app.php` en procesos PHP nuevos. | Escribe el script temporal `scripts/tmp/_measure_boot.php` y lo elimina después; no es un benchmark HTTP. |
| `scripts/benchmarks/01_internal.php` | Coste de llamadas a Schema, Model, Query Builder, DB, controllers y JSON. | Abre la conexión configurada y ejecuta lecturas y un `INSERT` en `perf_test`. |
| `scripts/benchmarks/02_db_pure.php` | PDO frente a `DB::select()`, Query Builder y Model. | Las consultas y conexiones no tienen condiciones iguales: PDO abre conexión por iteración y las rutas del framework pueden reutilizarla. El archivo incluye configuración literal de conexión y debe revisarse antes de cualquier ejecución. |
| `scripts/benchmarks/03_http.php` | Latencia de endpoints HTTP y percentiles. | Apunta a `http://simplerest.lan`; incluye CREATE, UPDATE y DELETE, por lo que puede modificar datos. Cuenta errores cURL, no todos los status HTTP como fallo. |
| `scripts/benchmarks/04_concurrency.php` | Lotes concurrentes con `curl_multi`. | Envía GET al mismo host fijo. Simula concurrencia desde PHP y no sustituye un generador de carga ni prueba el comportamiento de otro servidor. |
| `scripts/benchmarks/05_memory_classes.php` | Memoria, clases declaradas y archivos incluidos. | Hace bootstrap, abre conexión y consulta DB/Model; llama además una ruta de controller manual. |
| `scripts/benchmarks/06_cold_warm.php` | Compara un proceso nuevo con llamadas posteriores. | El camino frío cronometra bootstrap; el cálido cronometra una consulta DB. Las dos mediciones no cubren el mismo trabajo. |
| `scripts/benchmark.php` y `scripts/perf_benchmark.php` | Scripts de benchmark anteriores. | El primero repite `require_once` dentro del mismo proceso; el segundo manda solicitudes HTTP e incluye operaciones mutables. |

`scripts/benchmarks/run_all.php` ejecuta los siete scripts en secuencia. No lo trates como una comprobación inocua: su flujo encadena lecturas, escrituras y tráfico HTTP. El informe `reports/performance-benchmark-report.md` conserva resultados fechados, pero no fue reproducido y no se debe usar como promesa o comparación actual. Las limitaciones y discrepancias están clasificadas en el [ledger de auditoría](../audit/README.md#deployment-and-benchmark-evidence-2026-10-24).

## Qué falta para una medición comparable

Una cifra útil necesita un objetivo aislado y repetible. Antes de ejecutar una herramienta, define una base desechable con los fixtures esperados, un host de prueba dedicado y una estrategia de reinicio para los escenarios mutables. No ejecutes la suite contra datos de usuario o una aplicación compartida.

Registra junto con cada resultado:

- SHA exacto, fecha, sistema operativo, versión de PHP, SAPI y extensiones.
- Servidor, número de workers, configuración OPcache y estado de cachés.
- Motor y versión de DB, tamaño y estado de los datos, conexiones frías o reutilizadas.
- Escenario, verbo HTTP, payload, warmup, repeticiones y concurrencia.
- Status HTTP, errores de transporte, latencia por solicitud, mediana y percentiles calculados con un método declarado.

Para comparar dos rutas, mantén el mismo servidor, datos, esquema, payload, conexión, política de warmup y número de repeticiones. Mide bootstrap, query y HTTP como escenarios separados; el caso de referencia y el caso SimpleRest deben realizar el mismo trabajo. Conserva los datos crudos y no extrapoles una prueba local a producción.

## Estado de la evidencia

El informe guardado tiene fecha 2026-05-26 y contiene conclusiones numéricas, pero el método no permite reproducir una comparación equivalente con los scripts actuales. No se realizaron benchmarks en este trabajo. El README de la suite contiene un snapshot de entorno que también requiere actualización antes de usarse como descripción del entorno activo.

No se publica una cifra objetivo, una ventaja frente a otro framework ni una recomendación de infraestructura basada en ese informe. Para afirmar una mejora, conserva un baseline y repite ambos escenarios bajo la misma configuración.
