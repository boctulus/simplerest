# DB - Clase de Base de Datos

La clase DB es una librería clave cuyo rol principal es manejar las conexiones de base de datos y ofrecer información sobre las mismas. Posee además un mini Query Builder para consultas "raw".

## Obtención de información de drivers

| Método | Descripción |
|--------|-------------|
| `DB::driver()` | Devuelve driver de la conexión actual |
| `DB::driverVersion(bool $numeric)` | Devuelve la versión del driver |
| `DB::isMariaDB()` | Devuelve si es MariaDB |

**Ejemplo:**

```php
dd(DB::driver(), 'Driver');
dd(DB::driverVersion(), 'Driver version');
dd(DB::driverVersion(true), 'Driver version (num)');
dd(DB::isMariaDB(), 'Is MariaDB');
```

**Resultado:**

```
--[ Driver ]--
mysql

--[ Driver version ]--
5.7.35-0ubuntu0.18.04.2

--[ Driver version (num) ]--
5.7.35

--[ Is MariaDB ]--
false
```

## Ejecución de consultas crudas

Las consultas puramente crudas son aquellas que son un simple string en SQL que pueden contener los "?" para los parámetros en caso de que las consultas o sentencias sean preparadas.

### DB::select()

Se dispone del método `DB::select()`:

```php
$res = DB::select('SELECT * FROM products');
```

O pasando parámetros:

```php
$res = DB::select('SELECT * FROM products WHERE cost > ? AND size = ?', [550, '1 mm']);
```

### DB::statement()

El método `DB::statement()` intenta agregar el prefijo a las tablas para CREATE TABLE, ALTER TABLE, INSERT INTO, etc lo cual puede ser útil en algunos casos.

Si no se desea que se agregue nada simplemente deje 'tb_prefix' en null, false o como cadena vacía ('').

Si se utiliza `DB::statement()` en migraciones claramente habría agregado de prefijo.

**Ejemplo:**

```php
/**
* Run migration.
*
* @return void
*/
public function up()
{
    DB::statement("
    CREATE TABLE IF NOT EXISTS `my_table` (
        `id` int(11) PRIMARY KEY NOT NULL,
        `db` varchar(50) DEFAULT NULL,
        `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `created_at` DATETIME NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    DB::statement("
    ALTER TABLE `my_table`
        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}
```

## Query Builder - DB::table()

Usando la clase DB para obtener la instancia:

```php
$m = DB::table('products')
    ->select(['id', 'name', 'size'])
    ->where(['cost', 150, '>=']);

dd($m->get());
```

**Ventajas:**

- Es la opción menos verbosa.
- Se dispone del Schema para hacer validaciones.

## Transacciones

### Estructura básica

```php
DB::beginTransaction();

try {
    // operaciones sobre la base de datos

    DB::commit();

} catch(\Exception $e) {
    DB::rollback();
    dd($e->getMessage(), "Error en transacción");
}
```

### Forma conveniente con transaction()

```php
DB::transaction(function(){
    // operación sobre la base de datos
    // operación sobre la base de datos
    // operación sobre la base de datos
});
```

## Conexiones

### Configuración

En el archivo `config/databases.php` se definen las conexiones y el prefijo de las tablas. En WordPress por ejemplo por defecto es "wp_".

```php
DB::getConnection('woo3');
dd(DB::getTablePrefix());
```

### Métodos de conexión

- `DB::getDefaultConnection()` - Obtiene la conexión por defecto
- `DB::getConnection('robot')` - Obtiene una conexión específica
- `DB::getCurrentConnectionId()` - Obtiene el ID de la conexión actual

## Utilizar la clase DB desde config.php

Es de destacar que podría darse el caso de que sea Ud. necesite acceder a la base de datos desde el archivo config.php pero si se hiciera `DB::table('xxx')` se generaría una referencia circular porque se incluiría nuevamente el archivo config.php.

La solución es crear una conexión directamente sin uso de las clases Model o DB y luego especificar que el modelo no deba ni crear conexión ni intentar cargar el config.php para nada.

## Procedimientos almacenados

Para la ejecución de procedimientos almacenados se puede sacar ventaja de los métodos "raw" de la clase DB, en particular:

- `DB::statement()` - Para ejecución de sentencias que no devuelven resultado
- `DB::select()` - Para la ejecución de sentencias que devuelven resultado
- `DB::safeSelect()` - Similar a select() pero optimizado para SP donde se haga un fetchAll

## Notas importantes

- Las funciones "raw" de la clase DB admiten un parámetro para el tenant_id y en caso de tener que cambiar la conexión al finalizar la conexión original es restaurada.
- También se ha agregado soporte para prefijos en Schema, la clase principal para manejo de migraciones.