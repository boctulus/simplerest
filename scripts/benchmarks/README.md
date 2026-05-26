# SimpleRest Benchmark Suite

Ubicación: `scripts/benchmarks/`

## Benchmarks implementados

| # | Script | Qué mide |
|---|--------|----------|
| 00 | `00_bootstrap.php` | Bootstrap REAL: solo autoload + config + providers. Sin HTTP, sin request. |
| 01 | `01_internal.php` | Overhead interno PHP: Schema, Model, DB::select, Controller new, JSON encode. 1000-100000 iter. |
| 02 | `02_db_pure.php` | DB pura: PDO directo vs DB::select vs QueryBuilder vs Model::find. |
| 03 | `03_http.php` | HTTP completo: curl contra endpoints reales, percentiles, RPS. |
| 04 | `04_concurrency.php` | Concurrencia via curl_multi: throughput bajo carga, punto de saturación. |
| 05 | `05_memory_classes.php` | Memoria + clases declaradas + archivos incluidos por componente. |
| 06 | `06_cold_warm.php` | Cold (fresh PHP process) vs Warm (OPcache caliente). |

## Cómo ejecutar

```bash
# Todos los benchmarks
php scripts/benchmarks/run_all.php

# Uno específico
php scripts/benchmarks/01_internal.php
php scripts/benchmarks/03_http.php 200 20   # 200 iter, 20 warmup
php scripts/benchmarks/04_concurrency.php 20 500  # 20 concurrentes, 500 total
```

---

## Pendientes (no implementados)

### Por entorno / infraestructura

| Pendiente | Razón | Requisito |
|-----------|-------|-----------|
| Benchmark en Linux | Windows + Laragon distorsiona mediciones | Ubuntu/Docker con Nginx + PHP-FPM |
| OPcache on/off | PHP 8.3.15 actual no tiene extensión OPcache | Instalar `php-opcache` o usar PHP con OPcache |
| JIT tracing | No disponible sin OPcache | PHP 8.x + OPcache + `opcache.jit=tracing` |
| PHP-FPM vs CGI | Laragon usa CGI, no FPM | Migrar a PHP-FPM (nginx) |
| FrankenPHP / Swoole / RoadRunner | No instalados en el entorno | Instalar Swoole extension o FrankenPHP binary |

### Por herramientas de concurrencia

| Pendiente | Razón | Alternativa actual |
|-----------|-------|--------------------|
| ApacheBench (ab) | No instalado en Windows | `04_concurrency.php` (curl_multi, limitado) |
| wrk | No disponible en Windows | `04_concurrency.php` (proxy) |
| k6 | No instalado | `04_concurrency.php` |
| hey | No instalado | `04_concurrency.php` |

### Por alcance (cross-framework)

| Pendiente | Razón |
|-----------|-------|
| Comparar con Laravel | Requiere instalación de Laravel aparte + mismas condiciones |
| Comparar con Slim | Requiere instalación de Slim aparte |
| Comparar con Lumen | Framework discontinuado |
| Comparar con Express.js | Requiere Node.js + Express |
| Comparar con Fastify | Requiere Node.js + Fastify |

### Por integración externa

| Pendiente | Herramienta | Estado |
|-----------|-------------|--------|
| XHProf profiling | `pecl install xhprof` | No instalado |
| Tideways profiling | Extensión comercial | No disponible |
| Blackfire profiling | Servicio + extensión | No disponible |
| Xdebug profiling | `xdebug.mode=profile` | No configurado |

---

## Notas de entorno actual

- **OS:** Windows 11 + Laragon
- **PHP:** 8.3.15 (CGI, no FPM, no OPcache)
- **Server:** Apache (Laragon built-in)
- **DB:** MySQL 8.x local
- **Concurrencia:** solo via `curl_multi` (PHP userland, limitado)
- **Profiling:** ninguno (sin XHProf, Tideways, Blackfire, Xdebug profiler)
