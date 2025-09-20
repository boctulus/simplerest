# Model - Clase Base para Modelos

Un modelo extiende a la clase Model que les provee toda la funcionalidad y acepta un schema. Tanto modelos como schemas pueden ser generados por el comando "make".

La clase "base" para el modelo es Model pero se sugiere extender a MyModel a fin de poder aplicar campos automáticos y hooks de forma general o sea que apliquen a todos los modelos.

## Ejemplo de MyModel

```php
class MyModel extends Model
{
    protected $createdBy = 'usu_intIdCreador';
    protected $updatedBy = 'usu_intIdActualizador';
    protected $createdAt = 'gen_dtimFechaActualizacion';

    function __construct(bool $connect = false, $schema = null, bool $load_config = true){
        parent::__construct($connect, $schema, $load_config);
    }
}
```

En el ejemplo previo se especifica que para todos los modelos (salvo que en el propio modelo se explicite lo contrario) los campos automáticos que allí se listan (hay más) tienen los nombres mencionados.

## Formas de usar un Model

Hay básicamente 3 formas de usar un Model:

### a) Instanciando directamente Model

En cuyo caso *no* dispondríamos de un schema a menos que se lo inyectemos:

```php
$m = (new Model(true))
    ->table('products')
    ->select(['id', 'name', 'size'])
    ->where(['cost', 150, '>=']);

dd($m->get());
```

**Ventajas:**

- Puede hacerse uso del Query Builder sin un schema lo cual es útil para probar la validez del mismo (pruebas unitarias)
- Puede generar las queries sin una conexión a la base de datos (comportamiento por defecto).

**Desventajas:**

- Verboso
- En principio no se dispone del Schema sin lo cual no es posible hacer validaciones contra el mismo.
- No funcionan de forma automática los campos UUID

### b) Instanciando una clase Model derivada

```php
$m = (new ProductsModel(true))
    ->select(['id', 'name', 'size'])
    ->where(['cost', 150, '>=']);

dd($m->get());
```

### c) Usando la clase DB para obtener la instancia

```php
$m = DB::table('products')
    ->select(['id', 'name', 'size'])
    ->where(['cost', 150, '>=']);

dd($m->get());
```

**Ventajas:**

- Es la opción menos verbosa.
- Se dispone del Schema para hacer validaciones.

## Referencia de métodos de la clase Model

La clase Model responsable del Query Builder tiene una gran cantidad de métodos que proveen las distintas funcionalidades.

## Paginación

El framework ofrece varios métodos de paginación:

- `offset / limit`
- `take / skip` -- similar al anterior
- `paginate`

Con `paginate()` es directamente pasando la cantidad de páginas y el tamaño de página.

### Ejemplo con paginate()

```php
$page_size = $_GET['size'] ?? 10;
$page      = $_GET['page'] ?? 1;

DB::getConnection('az');

$rows = DB::table('products')
->paginate($page, $page_size)
->get();
```

Por otro lado la clase Paginator se encarga de generar el SQL para el modelo y ofrece métodos de cálculo de paginación.

En sí, `paginate()` es equivalente a llamar a `take()` y `offset()`:

```php
$rows = DB::table('products')
->take($page_size)
->offset($offset)
->get();
```

### Paginación a bajo nivel con Paginator

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

## Hooks

La clase Model provee varios hooks que pueden ser sobrescritos:

```php
public function onPuttingFolderBeforeCheck($id, $data, $folder){ }
public function onPuttingFolderAfterCheck($id, $data, $folder){ }
public function onPutFolder($id, $data, $folder, $affected){ }
```

Desde cualquiera de esos métodos es obviamente posible acceder a métodos y propiedades de visibilidad por lo menos protected de la clase Model y en particular a folder y id (del registro).

**Ejemplo:**

```php
function onGettingFolderBeforeCheck($id, $folder) {
    echo "Reading folder {$folder} with id={$id}";
}
```

## Conexiones

El método `DB::getConnection()` permite especificar opcionalmente el identificador de la conexión a base de datos a la que nos queremos conectar y la clase Model acepta una conexión:

```php
$conn = DB::getConnection('db2');
```

## Métodos de búsqueda de tablas

```php
/**
* @return string|null La tabla correspondiente o null si no se encuentra
*/
public function findTableByAlias($alias)
```