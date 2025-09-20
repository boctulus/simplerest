# Paginator - Clase para Paginación

Por otro lado la clase Paginator se encarga de generar el SQL para el modelo y ofrece métodos de cálculo de paginación.

En sí, `paginate()` es equivalente a llamar a `take()` y `offset()`:

```php
$rows = DB::table('products')
->take($page_size)
->offset($offset)
->get();
```

Pero ahora necesitamos saber cuándo vale offset.

## Paginación a bajo nivel

Existe otra forma de paginación "más a bajo nivel" que es manipulando directamente la clase Paginator.

**Ejemplo:**

```php
header('Content-Type: application/json; charset=utf-8');

$page_size = $_GET['size'] ?? 10;
$page      = $_GET['page'] ?? 1;

$offset = Paginator::calcOffset($page, $page_size);

DB::getConnection('az');

$rows = DB::table('products')
->take($page_size)
->offset($offset)
->get();

$row_count = DB::table('products')->count();

$paginator = Paginator::calc($page, $page_size, $row_count);
$last_page = $paginator['totalPages'];

return [
    "last_page" => $last_page,
    "data" => $rows
];
```

## Lógica de paginación

Para cálculo de offset y páginas se dispone de la clase Paginator tanto para PHP como JavaScript según se desee hacer los cálculos del lado del servidor o del cliente.

### Métodos disponibles

- `Paginator.calcOffset(currentPage, pageSize)`
- `Paginator.human2SQL(page, pageSize)`
- `Paginator.calc(currentPage, pageSize, rowCount)`