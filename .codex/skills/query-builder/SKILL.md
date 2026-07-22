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
DB::table('users')->get();
DB::table('users')->first();
DB::table('users')->find(145);
DB::table('users')->value('email');
DB::table('users')->pluck('email');
DB::table('users')->select(['id', 'name'])->get();
DB::table('users')->selectRaw('COUNT(*) as c')->get();
```

### Where Clauses

```php
DB::table('products')->where(['size' => '2L', 'cost' => 100])->get();
DB::table('products')->where([['cost', 200, '>=']])->get();
DB::table('products')->whereNot('process', 'worker')->get();
DB::table('products')->whereNull('workspace')->get();
DB::table('products')->whereNotNull('workspace')->get();
DB::table('products')->whereIn('size', ['0.5L', '3L'])->get();
DB::table('products')->whereBetween('cost', [100, 250])->get();
DB::table('products')->whereLike('name', '%a%')->get();
DB::table('products')->whereRegEx('name', 'Coke')->get();
DB::table('products')->whereColumn('firstname', 'lastname', '=')->get();
DB::table('products')->whereRaw('cost < IF(size = "1L", ?, 100)', [300])->get();
DB::table('products')->whereExists('(SELECT 1 FROM users WHERE ...)')->get();
```

### OR / Grouping

```php
DB::table('users')->where(['email' => $email])->orWhere(['username' => $username])->get();
DB::table('products')->group(function($q) {
    $q->where([['cost', 100, '>'], ['id', 50, '<']])
      ->orWhere([['cost', 100, '<='], ['description', NULL, 'IS NOT']]);
})->where(['belongs_to', 150, '>'])->get();
DB::table('products')->where(['belongs_to', 90])
    ->whereOr([['name', ['CocaCola', 'PesiLoca']], ['cost', 550, '>=']])->get();
DB::table('products')->not(function($q) { $q->whereRegEx('name', 'a$'); })->get();
```

### Joins

```php
DB::table('users')->join('user_sp_permissions', 'users.id', '=', 'user_sp_permissions.user_id')->get();
DB::table('users')->join('sp_permissions')->get();       // auto-join via schema
DB::table('users')->leftJoin('countries', 'countries.id', '=', 'users.country_id')->get();
DB::table('users', 'u')->join('products as p')->get();
// connectTo() — nested structured results
DB::table('courses')->where(['title', 'Calculus I'])->connectTo(['categories', 'users'])->get();
// joinTo() — flattened results
DB::table('courses')->joinTo(['categories', 'users'])->get();
```

### Insert

```php
DB::table('users')->create(['name' => 'John', 'age' => 22]);
DB::table('users')->insert([['name' => 'John'], ['name' => 'Jane']]);
DB::table('users')->createOrIgnore($data);
DB::table('users')->insertOrUpdate($data, ['email']);
DB::table('users')->bulkInsert($data, 1000);
DB::table('users')->rawInsert($data);
// JSON fields
DB::table('products')->create(['attributes' => ['color' => 'red']]);
```

### Update / Delete

```php
DB::table('users')->where(['id' => 1])->update(['name' => 'Nico']);
DB::table('products')->find(145)->touch();                    // touch updated_at
DB::table('products')->find(145)->delete();                   // soft delete
DB::table('products')->find(145)->forceDelete();              // permanent
DB::table('products')->setSoftDelete(false)->find(145)->delete();
DB::table('products')->find(145)->undelete();                 // restore
DB::table('products')->withTrashed()->get();
DB::table('products')->onlyTrashed()->get();
DB::table('products')->deleted()->get();                      // no soft-delete filter
```

### Transactions

```php
DB::beginTransaction();
try { DB::table('products')->create([...]); DB::commit(); }
catch (\Exception $e) { DB::rollback(); throw $e; }
```

### Pagination / Caching

```php
DB::table('products')->paginate($page, $page_size)->get();
DB::table('products')->take($page_size)->offset($offset)->get();
DB::table('users')->where('active', 1)->cached(60)->get();
DB::table('users')->cached()->get();                          // indefinite
```

### Debugging

```php
dd(DB::getLog());                                             // last SQL
$m = DB::table('users')->where(['id', 1]);
dd($m->dd());                                                 // dump SQL
$m->dontExec()->get();                                        // build SQL no execute
```

### Raw Queries / Stored Procedures

```php
DB::select('SELECT * FROM products WHERE cost > ?', [550]);
DB::insert('INSERT INTO products (name, cost) VALUES (?, ?)', ['X', 10]);
DB::statement("CALL insertEvent(?)", ['2012.01.01']);
DB::safeSelect("CALL partFinder(?)", [$s], 'ASSOC', null, $stmt);
```

### Driver Info

```php
DB::driver(); DB::driverVersion(); DB::isMariaDB();
```

## Common Pitfalls

1. `find()` needs schema — `table()` helper doesnt auto-detect PK
2. `String::contains($haystack, $needle)` — inverted vs PHP native
3. Dont mix associative and indexed arrays in same `where()`
4. Use `havingRaw()` for expressions like `COUNT(*)` in HAVING
5. Auto-joins: dont include pivot table, only target table
6. Multiple FK between same tables — aliases handled automatically
7. Fields auto-qualified (`table.field`). Use `dontQualify()` to disable
8. Use `wrap()` for reserved words like `key`
