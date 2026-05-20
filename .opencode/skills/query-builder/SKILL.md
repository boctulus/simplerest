---
name: query-builder
description: Complete reference for SimpleRest Query Builder with all methods, Laravel differences, pitfalls, and best practices.
---

# Query Builder Skill

Covers `DB::table()`, `table()`, Model QB, and all query/statement methods.

## Key Differences from Laravel

| Feature | SimpleRest | Laravel |
|---------|-----------|---------|
| Returns | Arrays (no objects) | Eloquent objects |
| `DB::table()` | Requires schema file | Works without schema |
| `table()` helper | Works without schema | N/A |
| `String::contains()` | `$haystack, $needle` | `$needle, $haystack` (reversed!) |
| ORM hydration | None (arrays) | Full hydration |

## Quick Reference

### Selecting

```php
DB::table('users')->get();              // all
DB::table('users')->first();            // first record
DB::table('users')->find(145);          // by PK (needs schema for PK name)
DB::table('users')->value('email');     // single column value
DB::table('users')->pluck('email');     // array of column values
DB::table('users')->select(['id', 'name'])->get();
DB::table('users')->selectRaw('COUNT(*) as c')->get();
```

### Where Clauses

```php
// Associative (AND)
DB::table('products')->where(['size' => '2L', 'cost' => 100])->get();

// Indexed with operator
DB::table('products')->where([['cost', 200, '>=']])->get();

// DO NOT mix associative + indexed in same where() call
// Multiple where() = AND
DB::table('products')
    ->where(['size', '2L'])
    ->where(['cost', 100, '<'])
    ->get();

DB::table('products')->whereNot('process', 'worker')->get();
DB::table('products')->whereNull('workspace')->get();
DB::table('products')->whereNotNull('workspace')->get();
DB::table('products')->whereIn('size', ['0.5L', '3L'])->get();
DB::table('products')->whereNotIn('size', ['0.5L', '3L'])->get();
DB::table('products')->whereBetween('cost', [100, 250])->get();
DB::table('products')->whereNotBetween('cost', [100, 250])->get();
DB::table('products')->whereLike('name', '%a%')->get();
DB::table('products')->whereRegEx('name', 'Coke')->get();
DB::table('products')->whereColumn('firstname', 'lastname', '=')->get();
DB::table('products')->whereRaw('cost < IF(size = "1L", ?, 100)', [300])->get();
DB::table('products')->whereExists('(SELECT 1 FROM users WHERE products.belongs_to = users.id)')->get();
```

### OR / Grouping

```php
// OR WHERE
DB::table('users')
    ->where(['email' => $email])
    ->orWhere(['username' => $username])
    ->get();

// Group (parentheses in WHERE)
DB::table('products')
    ->group(function($q) {
        $q->where([['cost', 100, '>'], ['id', 50, '<']])
          ->orWhere([['cost', 100, '<='], ['description', NULL, 'IS NOT']]);
    })
    ->where(['belongs_to', 150, '>'])
    ->get();

// whereOr — OR within a group
DB::table('products')
    ->where(['belongs_to', 90])
    ->whereOr([
        ['name', ['CocaCola', 'PesiLoca']],
        ['cost', 550, '>='],
    ])->get();

// NOT group
DB::table('products')
    ->not(function($q) {
        $q->whereRegEx('name', 'a$');
    })->get();

// Connectors: and(), or(), andNot(), orNot()
DB::table('xxxx')
    ->where(condA)
    ->or(function($q) {
        $q->where(condB)->where(condC);
    });
```

### Conditional Where (when)

```php
DB::table('migrations')
    ->when($to_db != DB::getDefaultConnectionId(), function($q) use($to_db) {
        $q->where(['db' => $to_db]);
    }, function($q) {
        $q->whereRaw('1');
    })
    ->delete();
```

### Joins

```php
// Explicit
DB::table('users')
    ->join('user_sp_permissions', 'users.id', '=', 'user_sp_permissions.user_id')
    ->get();

// Auto-join (requires schema with FK defined)
DB::table('users')->join('sp_permissions')->get();

// With pivot table: DON'T include the pivot table explicitly
DB::table('users')->join('sp_permissions')->get(); // OK
// NOT: ->join('user_sp_permissions')->join('sp_permissions')

DB::table('users')->leftJoin('countries', 'countries.id', '=', 'users.country_id')->get();
DB::table('users')->crossJoin('products')->get();
DB::table('users')->naturalJoin('department')->get();

// Aliases
DB::table('users', 'u')->join('products as p')->get();

// connectTo() — nested structured results
DB::table('courses')
    ->where(['title', 'Calculus I'])
    ->connectTo(['categories', 'users', 'tags'])
    ->get();
// Returns nested arrays: ['category' => [...], 'professor' => [...], 'users' => [...]]

// joinTo() — flattened results (like SQL joins)
DB::table('courses')
    ->joinTo(['categories', 'users', 'tags'])
    ->get();
```

### Insert

```php
// create() — simple insert, no transaction
$id = DB::table('users')->create(['name' => 'John', 'age' => 22]);

// insert() — with transaction, safer for bulk
$id = DB::table('users')->insert([
    ['name' => 'John', 'age' => 22],
    ['name' => 'Jane', 'age' => 25],
]);

// Ignore duplicates
DB::table('users')->createOrIgnore($data);
DB::table('users')->insertOrIgnore($data);

// Upsert
DB::table('users')->insertOrUpdate($data, ['email']);

// Bulk (optimized, single query)
DB::table('users')->bulkInsert($data, 1000);

// Raw (no hooks, no mutators)
DB::table('users')->rawInsert($data);

// JSON fields — pass array, auto-converted
DB::table('products')->create([
    'attributes' => ['color' => 'red', 'size' => 'large'],
]);
```

### Update

```php
DB::table('users')
    ->where(['firstname' => 'HHH', 'lastname' => 'AAA'])
    ->update(['firstname' => 'Nico', 'lastname' => 'Buzzi']);

// Touch updated_at only
DB::table('products')->find(145)->touch();
```

### Delete

```php
// Soft delete (if deleted_at column exists)
DB::table('products')->find(145)->delete();

// Force delete
DB::table('products')->find(145)->forceDelete();
DB::table('products')->setSoftDelete(false)->find(145)->delete();

// Restore soft-deleted
DB::table('products')->find(145)->undelete();

// Check if soft-deleted
$trashed = DB::table('products')->find(145)->trashed();

// Include soft-deleted
DB::table('products')->withTrashed()->get();
DB::table('products')->onlyTrashed()->get();

// Disable soft-delete filter entirely
DB::table('products')->deleted()->get();
```

### Transactions

```php
DB::beginTransaction();
try {
    DB::table('products')->create([...]);
    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
    throw $e;
}
```

### Pagination

```php
// Method 1: paginate()
DB::table('products')->paginate($page, $page_size)->get();

// Method 2: take/skip
DB::table('products')->take($page_size)->offset($offset)->get();

// Method 3: Paginator class
$offset = Paginator::calcOffset($page, $page_size);
$rows = DB::table('products')->take($page_size)->offset($offset)->get();
$paginator = Paginator::calc($page, $page_size, DB::table('products')->count());
```

### Ordering & Grouping

```php
DB::table('users')->orderBy(['name' => 'ASC'])->get();
DB::table('products')->groupBy(['size'])->get();

// Having
DB::table('products')
    ->groupBy(['size'])
    ->having(['c', 3, '>'])
    ->select(['name'])
    ->selectRaw('COUNT(*) as c')
    ->get();

// havingRaw() when having() fails (e.g., alias in HAVING)
DB::table('products')
    ->groupBy(['name'])
    ->selectRaw('COUNT(*) as c')
    ->havingRaw('c > ?', [3])
    ->get();

// Strict mode: force havingRaw() check
DB::table('products')->setStrictModeHaving(true)->having([...]);
```

### Caching

```php
// Cache results for 60s
DB::table('users')->where('active', 1)->cached(60)->get();

// Cache indefinitely
DB::table('users')->cached()->get();
```

### Debugging

```php
// Print last executed SQL
dd(DB::getLog());

// Print compiled SQL without executing
$m = DB::table('users')->where(['id', 1]);
dd($m->dd());        // dump SQL and return it
dd($m->toSql());     // return SQL string, no execution
$m->dontExec()->get();  // build SQL but don't run

// Get SQL log at any point
$log = DB::getLog();
```

### Scopes (Model methods)

```php
class ProductsModel extends MyModel {
    function costScope() {
        $this->where(['cost', 100, '>=']);
        return $this;
    }
}

DB::table('products')
    ->where(['id', 200, '>'])
    ->costScope()
    ->count();
```

### Hidden / Fillable Fields

```php
// In model
protected $hidden    = ['password'];
protected $not_fillable = ['confirmed_email', 'is_active'];

// At runtime
DB::table('users')->unhide(['password'])->hide(['confirmed_email']);
DB::table('users')->unhideAll();
DB::table('users')->fill(['email'])->unfill(['password']);
```

### Raw DB Queries

```php
DB::select('SELECT * FROM products WHERE cost > ?', [550]);
DB::insert('INSERT INTO products (name, cost) VALUES (?, ?)', ['X', 10]);
DB::update('UPDATE products SET cost = ? WHERE id = ?', [20, 1]);
DB::delete('DELETE FROM products WHERE id = ?', [1]);
DB::statement('DROP TABLE IF EXISTS temp');

// With connection override
DB::select('SELECT * FROM my_table', [], null, 'conn_2');
```

### Table Prefixes

Configured per-connection in `config/databases.php`:
```php
'tb_prefix' => 'wp_',  // WordPress-style prefix
```

Used automatically by `DB::statement()` for CREATE/ALTER/INSERT.

### Stored Procedures

```php
DB::statement("CALL insertEvent(?)", ['2012.01.01']);
DB::select('CALL productpricing(?)', [34]);
DB::safeSelect("CALL partFinder(?)", [$s], 'ASSOC', null, $stmt);

// With OUT parameters
$rows = DB::safeSelect("CALL partFinder(?, ?, ?, @rowCount)", [$s, $offset, $limit]);
$stmt = $conn->query("SELECT @rowCount as rowCount");
$result = $stmt->fetch(\PDO::FETCH_ASSOC);
```

### Driver Info

```php
DB::driver();             // e.g., 'mysql'
DB::driverVersion();      // e.g., '5.7.35-0ubuntu0.18.04.2'
DB::driverVersion(true);  // e.g., '5.7.35'
DB::isMariaDB();          // bool
```

## Common Pitfalls

1. **`find()` needs schema** — `table()` helper doesn't auto-detect PK; `DB::table()` does via schema
2. **`String::contains($haystack, $needle)`** — parameter order is inverted vs PHP native
3. **Mixing where styles** — don't combine associative and indexed arrays in same `where()`
4. **`having()` with aliases** — use `havingRaw()` when the field is an expression like `COUNT(*)`
5. **Pivot tables in auto-joins** — don't include the pivot table, just the target table
6. **Multiple FK between same tables** — auto-joins create aliases automatically (you can't override them)
7. **Qualification** — fields are auto-qualified (`table.field`). Use `dontQualify()` to disable
8. **Wrapping** — use `wrap()` to backtick-wrap reserved words like `key`

## Best Practices

- Use `DB::table()` over `table()` helper when you need schema-aware features (PK detection, hidden fields)
- Use `insert()` with transactions for multi-row inserts, `create()` for single
- Use `->cached(seconds)` for expensive queries
- Use `DB::getLog()` liberally during development
- Qualify fields in WHERE when using joins to avoid ambiguity
- Call `DB::closeConnection('id')` when done with ad-hoc connections
