# Paginación

La paginación se reparte entre tres capas: Query Builder fija límites SQL, `Paginator` calcula offsets o pagina arrays y la API/`Response` traduce parámetros y metadatos. Ninguna capa por sí sola prueba el resultado completo de una petición con base de datos.

## Query Builder

[`QueryBuilderTrait::paginate()`](../../src/framework/Traits/QueryBuilderTrait.php) fija `LIMIT` y `OFFSET`; el tamaño omitido viene de `paginator.default_limit` y usa 10 si no hay valor. El offset es `(page_size * page) - page_size`. No calcula el total, no valida que página y tamaño sean positivos, ni crea por sí solo `paginator` en la respuesta. Consulta [Query Builder](query-builder.md) para el flujo de ejecución y sus límites por driver.

## Clase `Paginator`

Los métodos estáticos [`calcOffset()`](../../src/framework/Libs/Paginator.php) y `human2SQL($page, $page_size)` traducen una página humana a offset/límite. `calc($current_page, $page_size, $row_count)` devuelve `total`, `count`, `currentPage`, `totalPages` y `pageSize`. La clase no rechaza tamaño cero o números de página fuera de rango antes de hacer los cálculos.

La instancia construye un sufijo SQL mediante `setOrders()`, `setOffset()`, `setLimit()`, `setAttr()` y `compile()`. `getQuery()` devuelve el fragmento y `getBinding()` las posiciones, valores y tipos PDO calculados; `page()` modifica límite y offset. No ejecuta el query ni crea una conexión. Hay una diferencia de estado en el constructor: guarda `$order` en `$this->order`, mientras `compile()` lee `$this->orders`; las llamadas repetidas a `compile()` también agregan bindings sin reiniciar el arreglo. Usa esta descripción como traza de fuente, no como una receta ejecutada.

`Paginator::paginate($data, $page, $page_size, $rows_path)` pagina un array o una cadena JSON; `rows_path` es una ruta de claves separadas por puntos hacia la lista. Devuelve la porción y reenvuelve el resultado bajo esa ruta. Es una utilidad distinta de `QueryBuilderTrait::paginate()` y no consulta la base de datos.

## API y metadatos de respuesta

El endpoint automático consume los nombres configurados bajo `paginator.params`, limita el tamaño con `paginator.max_limit` y, si se pide paginación sin página, usa la página 1. Su `Response` prepara metadatos con `setPaginatorParams()` y el formatter configurado. Consulta [parámetros de endpoints automáticos](../api/automatic-endpoints.md) y [Request y Response](../core/request-response.md) para esas capas.

`BootstrapPaginator` pertenece al paquete de formularios y renderiza HTML a partir de la respuesta; no es la clase `Core\Libs\Paginator`. Está descrito en [HTML y formularios](../services/forms.md).

## Evidencia

Los métodos y puntos de integración se trazaron en fuente; las pruebas de offsets de Query Builder se inspeccionaron, pero no se ejecutaron. No se hizo una consulta de base de datos ni se ejecutó la paginación de un array.
