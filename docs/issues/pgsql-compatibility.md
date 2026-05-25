# PGSQL Compatibility — Known Issues

> **State:** DQL functional, DDL not supported.
> See also `docs/PGSQL-Compatibility.md` for the full compatibility matrix and test instructions.

---

## 1. Schema.php — DDL Full Rewrite Required

**File:** `src/framework/Libs/Schema.php`

Schema.php is written 100% for MySQL. No conditional logic by driver exists in most methods. All DDL and metadata methods fail on PGSQL.

### Required changes per method

| Method | Problem |
|--------|---------|
| `createTable()` | Backticks, `ENGINE=InnoDB`, `DEFAULT CHARSET`. PGSQL does not support engine/charset at table level. |
| `showTable()` | `SHOW CREATE TABLE` is MySQL-only. PGSQL uses `pg_catalog.pg_class` + `pg_get_ddl()`. |
| `fromDB()` | Parsing of `SHOW CREATE TABLE` output to rebuild schema. No PGSQL equivalent. |
| `hasTable()` | Only `mysql`/`sqlite` cases. No PGSQL case. |
| `FKcheck()` | `SET FOREIGN_KEY_CHECKS` (MySQL). PGSQL requires `SET session_replication_role = 'replica'`. |
| `hasColumn()` | `SHOW COLUMNS FROM \`table\`` |
| `createDatabase()` | `CREATE DATABASE $name COLLATE $collation`. PGSQL does not use COLLATE. |
| `renameTable()` | `RENAME TABLE \`ori\` TO \`final\``. PGSQL uses `ALTER TABLE x RENAME TO y`. |
| `drop()` / `dropIfExists()` | Backtick-wrapped table names |
| `getCurrentDatabase()` | `SELECT DATABASE()`. PGSQL uses `SELECT current_database()`. |
| `existDatabase()` / `getDatabases()` | `SHOW DATABASES` or `information_schema.schemata` with MySQL columns |
| `getPKs()` | `SHOW INDEXES WHERE Key_name = 'PRIMARY'`. PGSQL uses `pg_catalog.pg_index`. |
| `getAutoIncrement()` / `getAutoIncrementField()` | `information_schema.tables.AUTO_INCREMENT` + `SHOW COLUMNS`. PGSQL uses sequences. |
| `getFKs()` / `getRelations()` | `information_schema.key_column_usage` with MySQL column names |
| `addIndex()` / `dropIndex()` | Backticks + MySQL index syntax |
| `addPrimary()` / `dropPrimary()` | Backticks |
| `addUnique()` / `dropUnique()` | Backticks + `UNIQUE KEY` vs `UNIQUE` |
| `addFullText()` / `dropFullText()` | FULLTEXT is MySQL-only. PGSQL uses GIN + `tsvector`. |
| `addSpatial()` / `dropSpatial()` | SPATIAL is MySQL-only. PGSQL uses PostGIS + `geometry`. |
| `dropForeign()` / `dropFK()` | Backticks + `DROP FOREIGN KEY` (MySQL). PGSQL uses `DROP CONSTRAINT`. |
| `change()` (modify column) | `ALTER TABLE ... CHANGE \`old\` \`new\`` (MySQL non-standard syntax). PGSQL uses `ALTER COLUMN ... TYPE` / `RENAME COLUMN`. |
| `getTableComment()` | MySQL `INFORMATION_SCHEMA.tables.table_comment`. |
| `getColumnComment()` | MySQL `INFORMATION_SCHEMA.columns.column_comment`. |
| `columnExists()` | `SHOW COLUMNS ... LIKE` |
| `addColumn()` | Backticks + MySQL column definition syntax |

### Fix approach

Each DDL/metadata method needs to:
1. Detect driver via `DB::driver()`
2. Generate PGSQL-compatible SQL:
   - No backticks → use `DB::quote()` (double quotes for PGSQL)
   - No `ENGINE=InnoDB`, no `DEFAULT CHARSET`
   - `AUTO_INCREMENT` → `SERIAL` / `GENERATED ALWAYS AS IDENTITY`
   - `SHOW` commands → `pg_catalog` / `information_schema` queries
   - FULLTEXT → GIN index + `tsvector` column
   - SPATIAL → PostGIS extension + `geometry`/`geography` type
   - `CHANGE column` → separate `ALTER COLUMN ... TYPE` (or `SET DATA TYPE`) + `RENAME COLUMN`

---

## 2. Schema-qualified name quoting

**File:** `src/framework/Libs/DB.php:1133-1187`, `src/framework/Traits/QueryBuilderTrait.php:484-486`

**Problem:** `DB::quote("schema.table")` wraps the entire string as a single identifier → `"schema.table"` instead of `"schema"."table"`.

**Flow:**
- `QueryBuilderTrait::buildFrom()` (line 484): if `schema != null`, concatenates `schema . '.' . table_name`
- That combined string goes through `DB::quote()` which wraps it entirely in `"`

**Workaround:** Set `'schema' => null` in connection config. PGSQL uses `public` schema by default.

**Fix:** `DB::quote()` should detect `.` and split:

```php
if (str_contains($str, '.')) {
    $parts = explode('.', $str);
    foreach ($parts as &$p) { $p = $d1 . $p . $d2; }
    return implode('.', $parts);
}
```

---

## 3. Float cast disabled in UPDATE

**File:** `src/framework/Traits/QueryBuilderTrait.php:3135-3154`

**Problem:** The `CAST(? AS DOUBLE PRECISION)` replacement is **commented out** inside the `bind()` block for UPDATE SET clauses. Doing division or float arithmetic in UPDATE on PGSQL may truncate to integer.

```php
// } elseif(DB::driver() == DB::PGSQL && is_float($val)){
//     $q = Strings::replaceNth('?', 'CAST(? AS DOUBLE PRECISION)', $q, $ix+1-$reps);
```

**Risk:** Division of columns in SET rounds to integer on PGSQL.

**Fix:** Uncomment with proper segmentation — apply cast only to values in SET, not to all `?` occurrences (including WHERE). Example:

```php
// Only apply to VALUES after SET, not WHERE placeholders
$isSetClause = str_contains($q, 'SET') && $ix > strpos($q, 'SET');
```

Or use explicit PGSQL casting in the query itself.

---

## 4. `getTableNames()` without PGSQL case

**File:** `src/framework/Libs/DB.php:337-362`

**Problem:** Only implemented for MySQL (`information_schema.tables WHERE table_schema = '$db_name'`). The PGSQL switch case is commented out.

**Required SQL:**
```sql
SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public';
```

---

## 5. `truncate()` uses backticks

**File:** `src/framework/Libs/DB.php:944-947`

**Problem:**
```php
static::statement("TRUNCATE TABLE `$table`");
```
Backticks are invalid in PGSQL. Additionally, PGSQL does not allow `TRUNCATE` on tables with FK references without `CASCADE`.

**Fix:** Use `DB::quote($table)` instead of raw backticks, and consider `CASCADE` support.

---

## 6. `safeSelect()` uses MySQL-only PDO flag

**File:** `src/framework/Libs/DB.php:933-938`

**Problem:**
```php
$conn->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
```
This PDO constant is MySQL-only. On PGSQL it throws `\PDOException: SQLSTATE[HY000]`.

**Impact:** Low — `safeSelect()` is only used in 3 places in the codebase. Avoid its use for PGSQL.

---

## 7. MySQL-only administration methods

**File:** `src/framework/Libs/DB.php`

These helpers use MySQL-specific SQL:

| Method | SQL |
|--------|-----|
| `status()` | `SHOW TABLE STATUS` |
| `optimize()` | `OPTIMIZE TABLE` |
| `repair()` | `REPAIR TABLE` |
| `dbLogStatus()` / `dbLogEnable()` / `dbLogDisable()` | MySQL `general_log` system variable |
| `setPasswOldMode()` / `setPasswNewMode()` | MySQL password plugins |

**Impact:** Low — administrative helpers not used in business logic.

---

## 8. `lastInsertId()` with PGSQL sequences

**File:** `src/framework/Libs/DB.php`

**Risk:** `lastInsertId()` in PGSQL requires a sequence name parameter (`table_name_id_seq`). If the framework calls `lastInsertId()` without arguments on PGSQL, it returns the last sequence value for the current session — but this may be incorrect if multiple tables were inserted into.

**Fix:** Use `lastInsertId('{table}_{column}_seq')` pattern for PGSQL.

---

## 9. PGSQL table name case sensitivity

PGSQL lowercases unquoted table names. The framework already lowercases names, so this should not be an issue. However, mixed-case names require quotes:

- `my_table` → `my_table` (same)
- `MyTable` → `mytable` (different from `"MyTable"`)
- `"MyTable"` → `MyTable` (preserves case)

**Risk:** Low if all table names are lowercase.

---

## Summary by Priority

| Priority | Issue | Effort |
|----------|-------|--------|
| 🔴 High | Schema.php DDL rewrite | Weeks |
| 🟡 Medium | Schema-qualified quoting | Hours |
| 🟡 Medium | Float cast in UPDATE | Hours |
| 🟡 Medium | `getTableNames()` PGSQL case | Minutes |
| 🟢 Low | `truncate()` backticks | Minutes |
| 🟢 Low | `safeSelect()` MySQL flag | Minutes |
| 🟢 Low | Admin methods | Won't fix |
| 🟢 Low | `lastInsertId()` sequences | Hours |
| 🟢 Low | Case sensitivity | Informational |
