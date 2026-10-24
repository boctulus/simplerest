# Constructor DDL de esquemas

[`Boctulus\Simplerest\Core\Libs\Schema`](../../src/framework/Libs/Schema.php) construye y ejecuta SQL DDL. Es distinto de los descriptores `ISchema` generados por `make schema` y del ciclo de [migraciones](../cli/README.md): el constructor no crea un archivo de migración ni ofrece por sí mismo un historial versionado de cambios. Esta referencia sigue la fuente; no se ejecutó DDL.

## Inicio y conexión

`new Schema($table)` aplica `Model::addPrefix()`, carga la lista de tablas y analiza la tabla actual en el constructor. Por eso requiere una conexión/configuración utilizables incluso antes de llamar a `dontExec()`. El valor inicial de motor es `InnoDB` y el charset `utf8`; no se comprobó compatibilidad con cada PDO.

```php
$schema = new Schema('products');
$schema
    ->increments('id')
    ->string('name', 100)->notNullable()
    ->decimal('price', 10, 2)->default(0)
    ->timestamps()
    ->create();
```

El fragmento ilustra la forma de la API y puede ejecutar `CREATE TABLE`; no se corrió. Úsalo solo con una conexión y un esquema de destino elegidos deliberadamente. Los ejemplos no sustituyen una migración revisada.

## Columnas

El builder selecciona una columna y encadena modificadores. La fuente declara estos tipos:

| Grupo | Métodos de tipo |
| --- | --- |
| Enteros y números | `int`, `integer`, `bigint`, `big`, `ubig`, `mediumint`, `smallint`, `tinyint`, `bit`, `decimal`, `float`, `double`, `real`, `serial` |
| Texto y binarios | `char`, `varchar`, `string`, `text`, `tinytext`, `mediumtext`, `longtext`, `binary`, `varbinary`, `blob`, `tinyblob`, `mediumblob`, `longblob` |
| Otros escalares | `boolean`, `bool`, `json`, `set`, `enum`, `time`, `year`, `date`, `datetime`, `timestamp` |
| Geometría | `point`, `multipoint`, `linestring`, `polygon`, `multipolygon`, `geometry`, `geometrycollection` |

`id()` crea un `BIGINT UNSIGNED` marcado como primary, pero no lo hace autoincremental. `increments()` agrega esa marca. `softDeletes()` agrega `deleted_at DATETIME NULL`; `datetimes()` agrega `created_at` y `updated_at`, y `timestamps()` delega a `datetimes()`.

Los modificadores de columna incluyen `auto()`, `nullable()`/`notNullable()`, `default()`, `currentTimestamp()`, `unsigned()`, `zeroFill()`, `binaryAttr()`, `charset()`, `collation()`/`collate()`, `comment()`/`commentField()`, `after()` y `first()`. `dropNullable()` también elimina el default; `dropAuto()` consulta la columna autoincremental actual. `setEngine()`, `setCharset()` y `setCollation()` guardan configuración de tabla; `createTable()` incorpora engine y charset en el SQL, pero no emite la collation de tabla almacenada.

`onUpdateCurrent()` está declarado, pero en esta versión pasa `current_timestamp()` a un setter que solo permite `UNSIGNED`, `UNSIGNED ZEROFILL` o `BINARY`; esa llamada lanza una excepción en fuente. `dropCommentField()` tiene el cuerpo vacío. No dependas de estos dos métodos como operaciones completas.

## Primary keys, índices y foreign keys

`primary()`/`pri()`, `unique()`, `index()`, `fulltext()` y `spatial()` marcan la columna actual. Para alteraciones hay métodos como `addPrimary()`, `addUnique()`, `setUnique()`, `addIndex()`, `addFullText()`, `setFullText()`, `addSpatial()`, y sus métodos `drop*`/`rename*` respectivos.

El camino de relación es `foreign($column)->references($field)->on($table)`. `foreignId()`, `fk()` y `fromField()` son aliases de `foreign()`; `toField()` aliasa `references()`; `constrained($table)` presupone la referencia `id`. `onDelete()` admite `CASCADE`, `RESTRICT`, `NO ACTION` y `SET NULL`; `onUpdate()` también acepta `SET DEFAULT`. En `createTable()` las foreign keys se compilan como sentencias `ALTER TABLE` separadas de la creación de tabla.

El SQL de algunos métodos de índice tiene límites visibles: `addSpatial()` construye `ALTER TABLE` sin nombre de tabla y `dropSpatial()` genera `DROP` de un identificador sin `INDEX`. No se ejecutaron estas operaciones. Revisa `dd()` y el SQL específico antes de considerar cualquier cambio.

## Crear, alterar y obtener SQL

`create()` llama a `createTable()`; `createIfNotExists()` usa `CREATE TABLE IF NOT EXISTS`. `createTable()` retorna `false` si la tabla ya existe y se ignoran warnings, y también retorna `false` si no hay campos. Puede lanzar `TableAlreadyExistsException` o `EmptySchemaException` si se desactiva esa política. La creación arma una sentencia principal y sentencias adicionales para índices y claves foráneas; por defecto pasa cada una a `DB::statement()` dentro de un bloque de transacción de la librería.

Para una tabla existente, los métodos de columna, índice y alteración preparan comandos que `alter()` compila mediante `change()`. La fuente expone operaciones de tabla/columna/índice como `dropTable()`, `dropTableIfExists()`, `truncateTable()`, `renameTableTo()`, `dropColumn()`, `renameColumn()`/`renameColumnTo()`, `dropIndex()`, `renameIndex()`/`renameIndexTo()`, `dropPrimary()`, `dropUnique()`, `dropFullText()`, `dropForeign()`/`dropFK()` y `addPrimary()`.

`dontExec()` evita la ejecución en los caminos `createTable()` y `change()`, pero no evita la conexión/introspección que hace el constructor. `dd()` devuelve el SQL compilado disponible y no acredita que ese SQL funcione en el driver. No trates `dontExec()` como una construcción offline ni como una protección uniforme para todos los métodos estáticos.

## Alcance del driver y lectura de metadatos

La implementación usa backticks y sintaxis MySQL en creación/alteración. La lectura de estructura usa rutas como `SHOW CREATE TABLE`, `SHOW INDEXES`, `SHOW COLUMNS` e `INFORMATION_SCHEMA`; `renameColumn()` tiene una rama condicional para MySQL 8. No es una promesa de compatibilidad general con todos los adaptadores que acepta `DB`.

También declara helpers estáticos de metadatos: `getCurrentDatabase()`, `getSelectedDatabase()`, `getPKs()`, `hasPK()`, `getAutoIncrement()`, `hasAutoIncrement()`, `getAutoIncrementField()`, `getFKs()`, `getRelations()`, `getAllRelations()`, `getDatabases()`, `existDatabase()`, `getTables()`, `getTableComment()`, `getColumnComment()`, `hasTable()`, `hasColumn()`, `FKcheck()`, `enableForeignKeyConstraints()`, `disableForeignKeyConstraints()`, `renameTable()`, `drop()`, `dropIfExists()`, `createDatabase()` y `getSchemaFiles()`. Sus nombres no garantizan que cada consulta opere en todos los motores.

## Índice de métodos públicos

Además de los helpers descritos arriba, la clase declara `tableExists`, `createStoreProcedure`, `dropStoreProcedure`, `columnExists`, `column`, `field`, `setEngine`, `setCharset`, `setCollation`, `getDefinition`, `constraint`, `references`, `on`, `constrained`, `onTable`, `toTable`, `onDelete`, `onUpdate`, `createTable`, `create`, `createIfNotExists`, `change`, `alter`, `dontExec`, `dd`, `getSchema`, `getCurrentSchema` y `generateMigrationFileName`.

[`SchemaBugsTest.php`](../../unit-tests/schema/SchemaBugsTest.php) contiene aserciones de SQL generado para algunas rutas de primary key e índices. Se inspeccionó su fuente, no se ejecutó PHPUnit; tampoco se abrió una base de datos ni se creó, alteró o borró una tabla en esta auditoría.

Consulta [descriptores de esquema](schemas.md) para los metadatos `ISchema`, [conexiones](connections.md) para el selector PDO, y el [ledger de DDL y API automática](../audit/README.md#claim-level-findings-schema-generation-and-automatic-api) para discrepancias y límites investigados.
