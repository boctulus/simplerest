---
title: Constructor DDL de esquemas
source:
  - docs/database/schema-builder.md
  - docs/audit/README.md
source_revision: cd60be5f8416cdcbbe7011e07486c4b65c0e0df2
status: partial
---

# Constructor DDL de esquemas

`Boctulus\\Simplerest\\Core\\Libs\\Schema` construye y ejecuta SQL DDL. Es distinto de los descriptores `ISchema` generados por `make schema` y del ciclo de [migraciones](/cli/routing): no crea un archivo de migración ni mantiene un historial versionado. Esta página sigue la fuente; no se ejecutó DDL.

## Inicio y conexión

`new Schema($table)` aplica el prefijo de `Model`, carga las tablas y analiza la tabla en el constructor. Por eso necesita configuración y conexión utilizables incluso antes de `dontExec()`. Los valores iniciales de motor y charset son `InnoDB` y `utf8`; no se verificó cada driver.

```php
$schema = new Schema('products');
$schema
    ->increments('id')
    ->string('name', 100)->notNullable()
    ->decimal('price', 10, 2)->default(0)
    ->timestamps()
    ->create();
```

El ejemplo puede ejecutar `CREATE TABLE`; no se corrió. Úsalo únicamente con una conexión y un esquema de destino seleccionados deliberadamente.

## Columnas y modificadores

La fuente declara estos grupos de tipos:

| Grupo | Métodos |
| --- | --- |
| Enteros y números | `int`, `integer`, `bigint`, `big`, `ubig`, `mediumint`, `smallint`, `tinyint`, `bit`, `decimal`, `float`, `double`, `real`, `serial` |
| Texto y binarios | `char`, `varchar`, `string`, `text`, `tinytext`, `mediumtext`, `longtext`, `binary`, `varbinary`, `blob`, `tinyblob`, `mediumblob`, `longblob` |
| Otros escalares | `boolean`, `bool`, `json`, `set`, `enum`, `time`, `year`, `date`, `datetime`, `timestamp` |
| Geometría | `point`, `multipoint`, `linestring`, `polygon`, `multipolygon`, `geometry`, `geometrycollection` |

`id()` crea `BIGINT UNSIGNED` como clave primaria, pero no autoincremental; `increments()` añade ese atributo. `softDeletes()` añade `deleted_at DATETIME NULL`; `datetimes()` añade `created_at` y `updated_at`, y `timestamps()` delega en `datetimes()`.

Los modificadores incluyen `auto()`, `nullable()`/`notNullable()`, `default()`, `currentTimestamp()`, `unsigned()`, `zeroFill()`, `binaryAttr()`, `charset()`, `collation()`/`collate()`, `comment()`/`commentField()`, `after()` y `first()`. `dropNullable()` también elimina el default; `dropAuto()` consulta la columna autoincremental actual. `setEngine()`, `setCharset()` y `setCollation()` almacenan configuración de tabla, pero la creación emite motor y charset, no la collation de tabla guardada.

Hay dos límites declarados en fuente: `onUpdateCurrent()` pasa `current_timestamp()` a un setter que solo permite `UNSIGNED`, `UNSIGNED ZEROFILL` o `BINARY`, así que esa llamada lanza una excepción; `dropCommentField()` está vacío. No dependas de esos métodos como operaciones completas.

## Claves e índices

`primary()`/`pri()`, `unique()`, `index()`, `fulltext()` y `spatial()` marcan la columna actual. Para alteraciones hay métodos `add*`, `set*`, `drop*` y `rename*` para índices y claves principales. Las relaciones se declaran con `foreign($column)->references($field)->on($table)`; `foreignId()`, `fk()` y `fromField()` son alias de `foreign()`, `toField()` de `references()`, y `constrained($table)` presupone que el campo referido es `id`.

`onDelete()` admite `CASCADE`, `RESTRICT`, `NO ACTION` y `SET NULL`; `onUpdate()` también acepta `SET DEFAULT`. En `createTable()` las claves foráneas se compilan en sentencias `ALTER TABLE` separadas.

La fuente tiene límites en las rutas espaciales: `addSpatial()` arma `ALTER TABLE` sin nombre de tabla y `dropSpatial()` no emite `DROP INDEX`. No se ejecutaron estas operaciones; inspecciona el SQL específico antes de cualquier uso.

## Crear, alterar y obtener SQL

`create()` delega a `createTable()` y `createIfNotExists()` usa `CREATE TABLE IF NOT EXISTS`. La creación puede retornar `false` si la tabla existe o no hay campos; con la política correspondiente desactivada puede lanzar `TableAlreadyExistsException` o `EmptySchemaException`. Arma una sentencia principal y sentencias adicionales para índices y claves foráneas; por defecto cada una se pasa a `DB::statement()` en un bloque de transacción de la librería.

Para tablas existentes, los métodos de columna, índice y alteración preparan comandos que `alter()` compila mediante `change()`. La clase expone `dropTable()`, `dropTableIfExists()`, `truncateTable()`, `renameTableTo()`, `dropColumn()`, `renameColumn()`/`renameColumnTo()`, `dropIndex()`, `renameIndex()`/`renameIndexTo()`, `dropPrimary()`, `dropUnique()`, `dropFullText()`, `dropForeign()`/`dropFK()` y `addPrimary()`.

`dontExec()` evita la ejecución en los caminos `createTable()` y `change()`, pero no evita la conexión ni la introspección del constructor. `dd()` devuelve el SQL compilado disponible; no demuestra que el driver lo acepte. Tampoco es un constructor completamente offline ni una protección uniforme para todos los métodos estáticos.

## Driver, metadatos y referencia

La implementación usa backticks y sintaxis MySQL al crear y alterar tablas. Lee estructura con rutas como `SHOW CREATE TABLE`, `SHOW INDEXES`, `SHOW COLUMNS` e `INFORMATION_SCHEMA`; `renameColumn()` tiene una rama condicional para MySQL 8. Esto no acredita compatibilidad general con todos los adaptadores aceptados por `DB`.

La clase también declara helpers estáticos para base de datos, claves, relaciones, tablas, columnas, comentarios, restricciones y creación de bases de datos. Además de los métodos explicados, la referencia fuente lista `tableExists`, `createStoreProcedure`, `dropStoreProcedure`, `columnExists`, `column`, `field`, `getDefinition`, `constraint`, `onTable`, `toTable`, `generateMigrationFileName` y `getCurrentSchema`. El nombre de un helper no garantiza que su consulta funcione en todos los motores.

Consulta [Descriptores de esquema](/database) para metadatos `ISchema`, [Query Builder](/database/query-builder) para consultas y [Routing CLI](/cli/routing) para el registro separado de comandos. `SchemaBugsTest.php` contiene aserciones de SQL generado para algunas rutas, pero se inspeccionó y no se ejecutó PHPUnit. No se abrió una base de datos ni se creó, alteró o borró una tabla en esta revisión.
