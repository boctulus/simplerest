---
title: Consultas SQL raw
source:
  - docs/database/raw-queries.md
  - docs/audit/README.md
source_revision: e0a3dc3f5843be258eed7d5cc1edec13031d5f87
status: partial
---

# Consultas SQL raw

## Lecturas

`DB::select()` prepara y ejecuta SQL en la conexión configurada. `DB::query()` es un alias. Por defecto aplica `Model::addPrefix()` y enlaza valores posicionales con `?`. La fuente acepta escalares y recursos; rechaza arrays y otros tipos. En la rama identificada como `pgsql`, castea parámetros float a `DOUBLE PRECISION`; los parámetros `null` se sustituyen por el token SQL `NULL` antes del binding.

`$only_one = true` devuelve una fila con `fetch()`; la ruta habitual usa `fetchAll()`. El cierre opcional del cursor se aplica sólo a `fetchAll()`. `selectOne()` fuerza siempre una sola fila y no usa su propio parámetro `$only_one`.

## `safeSelect()` no sanitiza

El comentario de código describe `safeSelect()` como una ayuda para cursores de procedimientos almacenados. En MySQL/MariaDB activa consultas buffered y delega en `select()`. No limpia ni valida el texto SQL. Además, pasa `$st` al séptimo argumento de `select()` (`$tb_prefix`) en vez del octavo argumento de referencia de statement; no confíes en el statement de salida ni en la aplicación del prefijo por esa ruta.

## Escrituras

- `insert()` aplica prefijos, prepara y enlaza valores, y luego intenta devolver el último identificador insertado. Para una clave explícita consulta una variable local `$data` no declarada, por lo que esa ruta no es un contrato fiable.
- `statement()` aplica prefijos, enlaza valores y devuelve `rowCount()` o `false`. Al capturar una excepción la registra y la vuelve a lanzar.
- `update()` y `delete()` llaman a `statement()`; no comprueban que el SQL corresponda al verbo del método.
- `truncate()` ejecuta `TRUNCATE TABLE` sin filtro de filas.

El parámetro llamado `$tenant_id` de estos métodos selecciona un identificador de conexión. No añade filtros de fila ni una política de acceso por tenant. El binding cubre valores, pero no nombres SQL ni fragmentos interpolados. Ninguna de estas escrituras o lecturas se ejecutó en esta revisión. No encontramos llamadas directas a `DB::safeSelect()` o `DB::selectOne()` en `unit-tests/`.
