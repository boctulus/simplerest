---
title: Base de datos
source:
  - docs/database/README.md
  - docs/database/connections.md
  - docs/database/query-builder.md
  - docs/database/model.md
  - docs/database/relations.md
  - docs/database/raw-queries.md
  - docs/database/transactions.md
  - docs/database/driver-compatibility.md
  - docs/database/schemas.md
  - docs/database/schema-builder.md
  - docs/database/migrations.md
source_revision: e0a3dc3f5843be258eed7d5cc1edec13031d5f87
status: partial
---

# Base de datos

Esta sección reúne conexiones, consultas, modelos, relaciones, SQL raw, transacciones, descriptores, DDL y migraciones. Los contratos descritos se trazaron en fuente; no se ejecutaron consultas, escrituras, transacciones, DDL ni migraciones. Las guías separadas cubren [Query Builder](/database/query-builder), [Model/ModelRecord](/database/model), [relaciones y subrecursos](/database/relations), [SQL raw](/database/raw-queries), [transacciones](/database/transactions), [compatibilidad por driver](/database/driver-compatibility), [constructor DDL](/database/schema-builder) y [migraciones](/database/migrations).

## Conexiones

La configuración combina `config/config.php` con `config/databases.php` y define conexiones junto con un identificador predeterminado. Cada entrada requiere `driver` y `db_name`; también puede definir host, puerto, usuario, contraseña, opciones PDO y charset.

`DB::setConnection($id)` selecciona una conexión configurada, pero no abre PDO. `DB::getConnection($id = null)` devuelve el handle cacheado o crea uno de forma diferida. Sin identificador actual, elige la única conexión configurada o el valor por defecto; con varias conexiones y sin default lanza una excepción. La construcción PDO tiene ramas para `mysql`, `sqlite`, `pgsql` y `sqlsrv`, y acepta los aliases `mariadb`, `postgres` y `mssql`. `DB::driver()` devuelve el valor configurado original, que algunas ramas comparan literalmente; los aliases no garantizan que todas las funciones por driver los reconozcan. Esto describe código, no extensiones instaladas ni compatibilidad global.

Este ejemplo representa la forma de la API; no se ejecutó contra una base configurada:

```php
DB::setConnection('main');
$pdo = DB::getConnection();
```

El parámetro `$tenant_id` de ciertos métodos `DB` se usa como identificador de conexión configurada. Eso no define aislamiento, propiedad ni un esquema por tenant. `withConnection()` y `withDefaultConnection()` retornan el resultado desde `try` y restauran una conexión previa en `finally` cuando existía; el bloque `finally` no retorna un valor. La propagación de excepciones no se volvió a ejecutar en esta revisión y un error durante la restauración podría afectar el resultado. Una transacción activa también impide cambiar o cerrar su conexión; consulta [transacciones](/database/transactions) y [límites por driver](/database/driver-compatibility).

## Query Builder

`DB::table($from, $alias = null, $connect = true)` resuelve un modelo de aplicación para un nombre de tabla; si recibe una expresión que contiene ` FROM ` usa `MyModel::fromRaw()`. La ruta depende del mapeo de modelos y de una conexión válida. Consulta el [catálogo completo de métodos y comportamientos](/database/query-builder).

- `select()` admite campos separados, un array o una cadena separada por comas.
- `where()` admite formas como `where('id', 7)`, `where('id', '>', 2)` y una condición en array.
- `toSql()` construye SQL y bindings; no ejecuta la consulta.
- En la ruta ordinaria de lectura, `get()` prepara, enlaza y ejecuta PDO cuando `$exec` está activo; con ejecución desactivada devuelve `false`.
- Si el esquema del modelo contiene `deleted_at`, se añade `deleted_at IS NULL` salvo que se habiliten los eliminados con `deleted()`.

`dontExec()` evita preparar y ejecutar la sentencia, pero no vuelve offline el camino estándar: `DB::table()` conecta por defecto y el binding puede conectar antes de consultar `$exec`. Cuando `first()` no encuentra fila, la fuente devuelve `[]`, no `null`; ese caso no se probó con una base real.

`paginate()` fija `LIMIT` y el offset `(tamaño * página) - tamaño`; usa `paginator.default_limit` o 10 si no se pasa un tamaño. No calcula total de filas ni devuelve metadatos de páginas. `count()`, `avg()`, `sum()`, `min()` y `max()` pasan por una implementación que castea a entero el escalar y también el `fetchAll()` de la ruta agrupada; no se debe asumir precisión decimal ni una colección agrupada.

::: warning Los modos preview/simulate no garantizan ausencia de escrituras
La fuente comprueba algunos modos en `create()` y `executeInsert()`, pero `update()` y `delete()` ejecutan con `$exec` activo sin comprobar el modo seleccionado. No trates `preview()` o `simulate()` como una garantía global de no escritura.
:::

## Relaciones, transacciones y SQL raw

`join()` puede inferir relaciones desde el esquema; `joinTo()` delega a esa ruta. `connectTo()` usa un cargador de subrecursos con subconsultas y agregados JSON, no una unión plana. Los pivotes pueden depender de un archivo generado y de un comando de escaneo. La [guía de relaciones](/database/relations) describe el cache incompleto y los límites del resultado; no se ejecutaron consultas relacionadas.

`DB::transaction()` administra la conexión seleccionada y usa savepoints sólo para ciertos valores exactos del driver. Un rollback anidado puede revertir toda la transacción si esa ruta no se activa. Consulta [transacciones](/database/transactions); su fuente y tests no se ejecutaron.

La [guía de SQL raw](/database/raw-queries) detalla el binding posicional, prefijos de tablas, métodos de escritura y operaciones sin filtro. `safeSelect()` no sanitiza SQL y su delegación actual coloca el argumento de statement en la posición de prefijo.

## Model y ModelRecord

`Model` compone la configuración de tabla, esquema y Query Builder. `ModelRecord` ofrece un estado por fila para hidratar resultados, detectar cambios y delegar la persistencia. Su contrato tiene diferencias concretas respecto de las consultas tradicionales, incluidas la hidratación con fetch asociativo, la clave primaria original y el posible borrado lógico. La [referencia de Model/ModelRecord](/database/model) describe esas ramas y sus límites.

## Descriptores de esquema

El generador produce una clase PHP que implementa `ISchema`; su `get()` devuelve metadatos de tabla, campos, tipos, reglas, clave primaria y relaciones. `make schema` inspecciona una tabla existente y escribe el descriptor: no crea la tabla ni una migración. Parte de la introspección usa SQL orientado a MySQL; un probe aislado con SQLite rechazó `SHOW COLUMNS`. El writer recibe `protected = false`, por lo que la fuente actual permite reemplazar un descriptor existente sin `--force`. No se ejecutó el generador ni DDL.

## Constructor DDL

`Schema` es otro componente: compila y ejecuta sentencias para crear o alterar tablas. No equivale a un descriptor `ISchema` ni a una migración con historial. Consulta la [referencia del constructor DDL](/database/schema-builder) para columnas, modificadores, índices, claves foráneas, métodos de alteración, introspección y límites específicos de la implementación. No se ejecutó DDL.

## Migraciones

`php com migrations` registra migraciones aplicadas y dispone de comandos para aplicar, revertir, repetir o restablecer cambios. La [referencia del ciclo de migraciones](/database/migrations) detalla el formato de los archivos, el tracking, los comandos y límites de `--simulate`. Se inspeccionó la fuente y se consultó la ayuda; no se ejecutaron migraciones ni DDL.

## Límites actuales

Escrituras, transacciones, consultas raw, paginación, agregados, relaciones, joins, migraciones y DDL siguen sin verificarse mediante ejecución de base de datos. Las guías explican rutas de fuente y hallazgos concretos, no una matriz completa de compatibilidad. Los tests consultados no se ejecutaron; `composer test` y la suite declarada en `phpunit.xml` incluyen `unit-tests/`, pero esto no aporta un resultado de ejecución. Con `debug` activo, una excepción de conexión puede registrar la entrada de configuración seleccionada con valores de credenciales; no expongas secretos en logs compartidos.
