---
title: Paginación
source:
  - docs/database/pagination.md
  - docs/database/query-builder.md
  - docs/api/automatic-endpoints.md
  - docs/core/request-response.md
source_revision: 0cd8a411195c4373648faf320416191e3018ecc3
status: partial
---

# Paginación

La paginación se divide entre límites SQL, la clase `Paginator`, los parámetros de la API y metadata de `Response`. Ninguna capa por sí sola demuestra una request completa con una base de datos.

## Query Builder

`QueryBuilderTrait::paginate($page, $page_size)` establece límite y offset. El offset es `(tamaño * página) - tamaño`; si falta tamaño, usa `paginator.default_limit` o 10. No cuenta registros ni crea metadata de página. Consulta [Query Builder](/database/query-builder) para el flujo de lectura y sus límites por driver.

## Clase `Paginator`

`calcOffset()` y `human2SQL()` traducen página y tamaño a offset/límite. `calc()` produce `total`, `count`, `currentPage`, `totalPages` y `pageSize`; la clase no valida tamaño cero ni páginas fuera de rango.

`setOrders()`, `setOffset()`, `setLimit()`, `setAttr()` y `compile()` construyen un sufijo SQL y bindings, sin ejecutar un query. El constructor guarda su argumento `$order` en un campo distinto del que `compile()` recorre; llamadas repetidas a `compile()` también acumulan bindings.

`Paginator::paginate($data, $page, $page_size, $rows_path)` pagina un array o JSON en memoria. `rows_path` señala la lista anidada con claves separadas por puntos. No accede a la base de datos.

## API y metadata

Los endpoints automáticos usan los nombres definidos en `paginator.params`, limitan el tamaño mediante `paginator.max_limit` y pueden usar página 1 por defecto. `Response::setPaginatorParams()` prepara metadata a través del formatter configurado. [Request y Response](/core/request-response) y [endpoints automáticos](/api/automatic-endpoints) describen esas capas.

`BootstrapPaginator` es otro componente: pertenece a los builders HTML y renderiza markup desde la respuesta. Consulta [HTML y formularios](/services/forms).

Se inspeccionaron las aserciones fuente para offsets; no se ejecutaron ni las pruebas, ni una consulta de base de datos, ni la paginación en memoria.
