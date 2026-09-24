# Migration Table Name Error

## Issue
When running migrations using `php com migrations migrate`, a "No table_name defined" error was encountered.

## Error Trace
```
Boctulus\Simplerest\Core\Model->from()
Boctulus\Simplerest\Core\Model->toSql()
Boctulus\Simplerest\Core\Model->exists()
Boctulus\Simplerest\Commands\MigrationsCommand->migrate()
```

## Cause
The error occurred because the `table_name` property of the Model instance was becoming `null` during a method chain involving `table()`, `where()`, `when()`, and `exists()`. Specifically, the chain:
```php
table('migrations')
    ->where(...)
    ->when(...)
    ->exists();
```
resulted in `exists()` being called on an instance where `table_name` was lost.

## Fix
The fix involved splitting the method chain into separate statements to ensure the Model instance retains its state correctly.

**Before:**
```php
$exists = table('migrations')
    ->where(['filename' => $filename])
    ->when(...)
    ->exists();
```

**After:**
```php
$query = table('migrations');
$query->where(['filename' => $filename]);
$query->when(...);
$exists = $query->exists();
```

This change ensures that the `$query` object (the Model instance) is correctly initialized and modified before `exists()` is called.
