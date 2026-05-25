# PGSQL Compatibility — SimpleRest Query Builder

Status: **DQL functional, DDL not supported**

## Overview

SimpleRest Query Builder supports PostgreSQL (DQL) with some caveats. Schema Builder (DDL) is MySQL-only.

---

## Connection Setup

### 1. PostgreSQL Container

Pre-existing `D:\Docker\supabase-*` and `llcbuilder-postgres` containers available.
A dedicated `pg-test` container was created:

```
D:\Docker\pg-test\docker-compose.yml
```

- Port: `54320`
- User: `postgres`
- Password: `postgres`
- DB: `pgtest`

### 2. PHP Extensions

Requires `pdo_pgsql` and `pgsql` enabled in `php.ini`:

```ini
extension=pdo_pgsql
extension=pgsql
```

### 3. Connection Config

Add to `config/databases.php`:

```php
'pg_test' => [
    'driver'     => 'pgsql',
    'host'       => '127.0.0.1',
    'port'       => 54320,
    'db_name'    => 'pgtest',
    'user'       => 'postgres',
    'pass'       => 'postgres',
    'charset'    => 'utf8',
    'schema'     => null,
    'pdo_options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
],
```

`schema => null` avoids schema-qualified table names (uses default `public` schema).

### 4. Usage

```php
DB::getConnection('pg_test');

$rows = DB::table('my_table', null, true, 'pg_test')->get();
```

---

## Compatibility Matrix

### DB.php — Good for DQL

| Feature | Status | Details |
|---------|--------|---------|
| PDO DSN | ✅ | `pgsql:host=$host;dbname=$db_name;port=$port` |
| Driver alias | ✅ | `postgres` → `pgsql` |
| Charset | ✅ | Via `SET NAMES` |
| `quote()` | ✅ | Uses `"` double quotes |
| `random()` | ✅ | `ORDER BY RANDOM()` |
| Savepoints | ✅ | `supportsSavepoints()` includes PGSQL |
| Float cast (SELECT) | ✅ | `CAST(? AS DOUBLE PRECISION)` |
| Float cast (UPDATE) | ❌ | Commented out in QueryBuilderTrait line 3145 |
| `getTableNames()` | ❌ | PGSQL case commented out (line 345) |
| `truncate()` | ❌ | Uses backtick syntax |
| `status()`, `optimize()`, `repair()` | ❌ | MySQL-only |
| `safeSelect()` | ❌ | Uses `PDO::MYSQL_ATTR_USE_BUFFERED_QUERY` |
| `dbLog*()` | ❌ | MySQL `general_log` only |

### Schema.php — NOT Compatible (DDL) ❌

Schema.php está escrito 100% para MySQL. No hay lógica condicional por driver en casi ningún método. A continuación se detalla cada problema:

| Feature | Línea(s) | Problema concreto |
|---------|----------|-------------------|
| `createTable()` | 1293-1409 | Usa backticks `` ` ``, requiere `ENGINE=InnoDB`, `DEFAULT CHARSET`. PGSQL no soporta engine ni charset a nivel tabla. |
| `showTable()` | 1273-1280 | `SHOW CREATE TABLE` es MySQL-only. PGSQL usa `pg_catalog.pg_class` + `pg_get_ddl()`. |
| `fromDB()` | 1667-1981 | Parseo de `SHOW CREATE TABLE` para reconstruir schema. No existe equivalente en PGSQL. |
| `hasTable()` | 480-497 | Solo tiene casos `mysql` (SHOW TABLES LIKE / information_schema.tables) y `sqlite` (sqlite_master). Sin case PGSQL. |
| `FKcheck()` | 449-464 | `SET FOREIGN_KEY_CHECKS` (MySQL) / `PRAGMA foreign_keys` (SQLite). PGSQL requiere `SET session_replication_role = 'replica'`. |
| `hasColumn()` | 500-505 | `SHOW COLUMNS FROM \`table\``. PGSQL usa `information_schema.columns` con sintaxis diferente. |
| `createDatabase()` | 1268-1271 | `CREATE DATABASE $name COLLATE $collation`. PGSQL no usa COLLATE. |
| `renameTable()` | 507-510 | `RENAME TABLE \`ori\` TO \`final\``. PGSQL usa `ALTER TABLE x RENAME TO y`. |
| `drop()` / `dropIfExists()` | — | Usa backticks `` `table` ``. PGSQL no acepta backticks. |
| `getCurrentDatabase()` | — | `SELECT DATABASE()`. PGSQL usa `SELECT current_database()`. |
| `existDatabase()` | — | `SHOW DATABASES` o `information_schema.schemata`. |
| `getDatabases()` | — | `SHOW DATABASES` sin alternativa PGSQL. |
| `getPKs()` | — | `SHOW INDEXES WHERE Key_name = 'PRIMARY'`. PGSQL usa `pg_catalog.pg_index`. |
| `getAutoIncrement()` | — | `information_schema.tables.AUTO_INCREMENT`. PGSQL usa `SERIAL` / secuencias. |
| `getAutoIncrementField()` | — | `SHOW COLUMNS ... LIKE '%auto_increment%'`. |
| `getFKs()` | — | `information_schema.key_column_usage` con column names MySQL. |
| `getRelations()` | — | Depende de `getFKs()`, hereda la limitación. |
| `addIndex()` / `dropIndex()` | 1488-1516 | `ADD INDEX (\`name\`)` con backticks. PGSQL usa `USING` e índice sin backticks. |
| `addPrimary()` / `dropPrimary()` | 1520-1581 | `ADD PRIMARY KEY (\`name\`)`. PGSQL idem sin backticks. |
| `addUnique()` / `dropUnique()` | 1588-1609 | `ADD UNIQUE KEY \`name\` (\`name\`)`. Sintaxis diferente. |
| `addFullText()` / `dropFullText()` | 1616-1636 | MySQL-only (FULLTEXT no existe en PGSQL). PGSQL usa GIN + `tsvector`. |
| `addSpatial()` / `dropSpatial()` | 1639-1653 | MySQL-only (SPATIAL no existe en PGSQL). PGSQL usa PostGIS + `geometry`. |
| `dropForeign()` / `dropFK()` | 1656-1664 | `ALTER TABLE \`t\` DROP FOREIGN KEY \`fk\`` con backticks. |
| `change()` (modify column) | 2070+ | `ALTER TABLE ... CHANGE \`old\` \`new\` ...` (MySQL syntax, no SQL standard). |
| `getTableComment()` | — | MySQL `information_schema.tables.table_comment`. |
| `getColumnComment()` | — | MySQL `information_schema.columns.column_comment`. |
| `columnExists()` | — | `SHOW COLUMNS ... LIKE` (MySQL-only). |
| `addColumn()` | — | `ALTER TABLE \`t\` ADD \`col\` ...` con backticks. |

**Conclusión:** Schema.php requeriría una refactorización completa para soportar PGSQL. Cada método DDL necesitaría:
1. Detectar driver (`DB::driver()`)
2. Generar SQL con sintaxis PGSQL adecuada (sin backticks, sin ENGINE/CHARSET, sin FULLTEXT/SPATIAL)
3. Usar `SERIAL` para auto-increment en vez de `AUTO_INCREMENT`
4. Para metadatos: reemplazar `SHOW` commands por queries a `pg_catalog` / `information_schema` (column names PGSQL)
5. FULLTEXT y SPATIAL son extensiones PGSQL (no nativas) y requerirían aproximación diferente

### QueryBuilderTrait — Good for DQL ✅

| Feature | Status | Notes |
|---------|--------|-------|
| `get()` | ✅ | |
| `first()` | ✅ | |
| `find()` | ✅ | Requires schema/model |
| `where()` | ✅ | |
| `whereIn/whereNotIn` | ✅ | |
| `whereNull/whereNotNull` | ✅ | |
| `whereBetween` | ✅ | |
| `orderBy()` | ✅ | Array-based syntax |
| `limit/offset` | ✅ | |
| `paginate()` | ✅ | |
| `create()` | ✅ | INSERT without backticks when not MySQL |
| `update()` | ✅ | |
| `delete()` | ✅ | |
| `join()` | ✅ | |
| `selectRaw()` | ✅ | |
| `count/max/min/avg/sum` | ✅ | |
| `groupBy/having` | ✅ | |
| `transactions` | ✅ | With savepoints |
| Schema-qualified names | ⚠️ | `DB::quote()` wraps `schema.table` as single identifier |
| Float cast (UPDATE) | ⚠️ | Commented out, may cause division errors |

### Paginator — Compatible ✅

| Method | Status |
|--------|--------|
| `LIMIT ?` | ✅ |
| `OFFSET ? LIMIT ?` | ✅ |
| `OFFSET ?` only | ✅ |

### SubResourceHandler — Partial ⚠️

- Has PGSQL case using `array_to_string(array_agg(...))`
- Should use `json_agg()` for PostgreSQL ≥ 9.4

### Model.php — Neutral ✅

No driver-specific code. All logic delegated to traits.

---

## Known Issues & Fixes

### 1. Schema.php — DDL Full Rewrite Required ❌

Schema.php está escrito 100% para MySQL. No hay lógica condicional por driver en casi ningún método. Todos los métodos DDL y de metadatos fallan en PGSQL.

**Solución:** Requiere refactorización completa. Cada método necesita:
1. Detectar driver con `DB::driver()`
2. Generar SQL con sintaxis PGSQL (sin backticks, sin `ENGINE=InnoDB`, sin `CHARSET`, sin `FULLTEXT`/`SPATIAL`)
3. `AUTO_INCREMENT` → `SERIAL` / `GENERATED ALWAYS AS IDENTITY`
4. `SHOW` commands → queries a `pg_catalog` / `information_schema` (columnas PGSQL)
5. FULLTEXT y SPATIAL son extensiones PGSQL (no nativas) y requieren aproximación diferente

Problemas concretos por método:

| Método(s) | Problema |
|-----------|----------|
| `createTable()` | Backticks, `ENGINE=InnoDB`, `DEFAULT CHARSET`. PGSQL no soporta engine/charset a nivel tabla. |
| `showTable()` | `SHOW CREATE TABLE` es MySQL-only. PGSQL usa `pg_catalog.pg_class` + `pg_get_ddl()`. |
| `fromDB()` | Parseo de `SHOW CREATE TABLE` para reconstruir schema. Sin equivalente PGSQL. |
| `hasTable()` | Solo cases `mysql`/`sqlite`. Sin case PGSQL. |
| `FKcheck()` | `SET FOREIGN_KEY_CHECKS` (MySQL). PGSQL requiere `SET session_replication_role = 'replica'`. |
| `hasColumn()` | `SHOW COLUMNS FROM \`table\`` |
| `createDatabase()` | `CREATE DATABASE $name COLLATE $collation`. PGSQL no usa COLLATE. |
| `renameTable()` | `RENAME TABLE \`ori\` TO \`final\``. PGSQL usa `ALTER TABLE x RENAME TO y`. |
| `drop()` / `dropIfExists()` | Backticks en nombre de tabla |
| `getCurrentDatabase()` | `SELECT DATABASE()`. PGSQL usa `SELECT current_database()`. |
| `existDatabase()` / `getDatabases()` | `SHOW DATABASES` o `information_schema.schemata` con columnas MySQL |
| `getPKs()` | `SHOW INDEXES WHERE Key_name = 'PRIMARY'`. PGSQL usa `pg_catalog.pg_index`. |
| `getAutoIncrement()` / `getAutoIncrementField()` | `information_schema.tables.AUTO_INCREMENT` + `SHOW COLUMNS`. PGSQL usa secuencias. |
| `getFKs()` / `getRelations()` | `information_schema.key_column_usage` con column names MySQL |
| `addIndex()` / `dropIndex()` | Backticks + sintaxis MySQL para índices |
| `addPrimary()` / `dropPrimary()` | Backticks |
| `addUnique()` / `dropUnique()` | Backticks + `UNIQUE KEY` vs `UNIQUE` |
| `addFullText()` / `dropFullText()` | FULLTEXT es MySQL-only. PGSQL usa GIN + `tsvector`. |
| `addSpatial()` / `dropSpatial()` | SPATIAL es MySQL-only. PGSQL usa PostGIS + `geometry`. |
| `dropForeign()` / `dropFK()` | Backticks + `DROP FOREIGN KEY` (MySQL). PGSQL usa `DROP CONSTRAINT`. |
| `change()` (modify column) | `ALTER TABLE ... CHANGE \`old\` \`new\`` (sintaxis MySQL no-standard). PGSQL usa `ALTER COLUMN ... TYPE` / `RENAME COLUMN`. |
| `getTableComment()` | MySQL `INFORMATION_SCHEMA.tables.table_comment`. |
| `getColumnComment()` | MySQL `INFORMATION_SCHEMA.columns.column_comment`. |
| `columnExists()` | `SHOW COLUMNS ... LIKE` |
| `addColumn()` | Backticks + MySQL column definition syntax |

### 2. Schema-qualified name quoting ⚠️

**Archivo:** `DB.php:1133-1187`, `QueryBuilderTrait.php:484-486`

**Problema:** `DB::quote("schema.table")` envuelve todo como un solo identificador → `"schema.table"` en vez de `"schema"."table"`.

**Flujo:**
- `QueryBuilderTrait::buildFrom()` (línea 484): si `schema != null`, concatena `schema . '.' . table_name`
- Ese string combinado pasa a `DB::quote()` que lo rodea entero con `"`

**Workaround:** Usar `'schema' => null` en la conexión. PGSQL usa `public` por defecto.

**Fix ideal:** `DB::quote()` debería detectar el `.` y dividir:

```php
if (str_contains($str, '.')) {
    $parts = explode('.', $str);
    foreach ($parts as &$p) { $p = $d1 . $p . $d2; }
    return implode('.', $parts);
}
```

### 3. Float cast deshabilitado en UPDATE ⚠️

**Archivo:** `QueryBuilderTrait.php:3135-3154`

**Problema:** El cast `CAST(? AS DOUBLE PRECISION)` está **comentado** dentro del bloque `bind()` para la cláusula SET del UPDATE. Si se hace una división o aritmética con floats en un UPDATE, PGSQL puede truncar a entero.

```php
// } elseif(DB::driver() == DB::PGSQL && is_float($val)){ 
//     $q = Strings::replaceNth('?', 'CAST(? AS DOUBLE PRECISION)', $q, $ix+1-$reps);
```

**Riesgo:** División de columnas en SET puede redondear a entero.

**Fix:** Descomentar con segmentación: aplicar cast solo a VALUES en SET, no a todas las ocurrencias de `?` (incluyendo WHERE). O usar casting PGSQL explícito en las queries.

### 4. `getTableNames()` sin case PGSQL ❌

**Archivo:** `DB.php:337-362`

**Problema:** Solo implementado para MySQL (`information_schema.tables WHERE table_schema = '$db_name'`). El case PGSQL está comentado. PGSQL requiere:

```sql
SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public';
```

### 5. `truncate()` usa backticks ❌

**Archivo:** `DB.php:944-947`

**Problema:**
```php
static::statement("TRUNCATE TABLE `$table`");
```
Los backticks no son válidos en PGSQL. Además, PGSQL no permite `TRUNCATE` en tablas con FK references sin `CASCADE`.

**Fix:** Usar `DB::quote($table)` en vez de backticks directos.

### 6. `safeSelect()` usa flag MySQL-only ❌

**Archivo:** `DB.php:933-938`

**Problema:**
```php
$conn->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
```
Esta constante PDO es exclusiva de MySQL. En PGSQL lanza `\PDOException: SQLSTATE[HY000]`.

**Impacto:** Bajo — `safeSelect()` solo se usa en 3 lugares del código base. Para PGSQL hay que evitar su uso.

### 7. Métodos de administración MySQL-only ❌

- `DB::status()` — Ejecuta `SHOW TABLE STATUS`
- `DB::optimize()` — Ejecuta `OPTIMIZE TABLE`
- `DB::repair()` — Ejecuta `REPAIR TABLE`
- `DB::dbLogStatus()`, `DB::dbLogEnable()`, `DB::dbLogDisable()` — Usan `general_log` de MySQL
- `DB::setPasswOldMode()`, `DB::setPasswNewMode()` — MySQL password plugins

**Impacto:** Bajo. Son helpers administrativos no usados en lógica de negocio.

### 8. `DB::insert()` con backticks en `lastInsertId()` ⚠️

**Archivo:** `DB.php` zona de `insert()` / `lastInsertId()`

**Riesgo:** Si se usa `lastInsertId()` con seqname PGSQL (ej. `pg_test_products_id_seq`), requiere pasar el nombre de secuencia explícito.

### 9. Caso de tablas en PGSQL ⚠️

PGSQL lowercasea los nombres de tablas a menos que se usen quotes. El framework ya lowercasea los nombres, por lo que no debería ser problema. Sin embargo, si se usan nombres con mayúsculas, PGSQL interpreta `"MyTable"` ≠ `mytable`.

---

## Running Tests

### MySQL (all should pass)

```bash
php vendor/bin/phpunit unit-tests/query-builder/ModelTest.php --no-configuration
php vendor/bin/phpunit unit-tests/query-builder/DB_TransactionTest.php --no-configuration
```

### PGSQL

Pre-requisites:
1. Set connection to `pg_test` (or adjust ModelTest to switch connection)
2. Ensure `pdo_pgsql` extension is enabled
3. PostgreSQL container running

---

## Author

Pablo Bozzolo (boctulus)
