---
title: Rendimiento y benchmarks
source:
  - docs/performance/README.md
source_revision: 7354ea996d206b90398a4cd0e5e5526711182a5e
status: partial
---

# Rendimiento y benchmarks

Esta guía describe herramientas de medición del checkout y sus límites. No declara una cifra vigente ni compara SimpleRest con otro framework. No se ejecutaron estos scripts durante esta auditoría porque varios hacen escrituras o envían solicitudes mutables.

## Scripts presentes

| Archivo | Intención | Límite o efecto observado |
| --- | --- | --- |
| `scripts/benchmarks/00_bootstrap.php` | Medir bootstrap de `app.php` en procesos nuevos. | Escribe y elimina un archivo temporal bajo `scripts/tmp/`; no mide HTTP. |
| `scripts/benchmarks/01_internal.php` | Medir Schema, Model, Query Builder, DB, controllers y JSON. | Abre la conexión configurada y lee/escribe en `perf_test`. |
| `scripts/benchmarks/02_db_pure.php` | Comparar PDO, `DB::select()`, Query Builder y Model. | PDO abre una conexión por iteración, mientras el framework puede reutilizarla. El código contiene configuración de conexión que hay que revisar antes de ejecutarlo. |
| `scripts/benchmarks/03_http.php` | Medir latencia y percentiles HTTP. | Apunta a `http://simplerest.lan` y envía CREATE, UPDATE y DELETE. Registra errores cURL, pero no trata todos los status HTTP como error. |
| `scripts/benchmarks/04_concurrency.php` | Enviar lotes con `curl_multi`. | Sólo usa GET contra el host fijo; simula concurrencia desde PHP y no equivale a una prueba de carga del servidor. |
| `scripts/benchmarks/05_memory_classes.php` | Observar memoria, clases y archivos incluidos. | Hace bootstrap y consultas a DB/Model. |
| `scripts/benchmarks/06_cold_warm.php` | Comparar ejecución fría y posterior. | Compara bootstrap nuevo con una consulta DB ya cargada; no mide el mismo trabajo en ambos casos. |
| `scripts/benchmark.php` y `scripts/perf_benchmark.php` | Scripts anteriores. | El primero cronometra repetidos `require_once` en el mismo proceso; el segundo envía tráfico HTTP con operaciones de escritura. |

`scripts/benchmarks/run_all.php` lanza la suite completa, que combina DB, archivos temporales y tráfico HTTP. No es una comprobación inocua y no debe apuntarse a datos de usuario ni a una aplicación compartida.

## Preparar una comparación

Una medición requiere una base desechable con los fixtures esperados, un host de prueba propio y un método para restablecer los escenarios mutables. Registra el SHA, fecha, sistema operativo, PHP/SAPI/extensiones, servidor y workers, OPcache, motor y datos de DB, estado frío/caliente, escenario, método, payload, warmup, repeticiones, concurrencia, status HTTP y errores de transporte.

Compara ambos caminos con el mismo servidor, esquema, datos, payload, conexión, warmup y repeticiones. Separa bootstrap, consulta y HTTP; conserva las muestras crudas y calcula percentiles con un método declarado. No extrapoles una prueba local a producción.

## Estado del informe existente

El informe en `reports/performance-benchmark-report.md` tiene fecha 2026-05-26 y contiene conclusiones numéricas que no se reprodujeron. Los benchmarks actuales no respaldan una comparación equivalente, así que sus valores se consideran históricos y no son una promesa vigente. No se publica una cifra objetivo ni una ventaja frente a otro framework.
