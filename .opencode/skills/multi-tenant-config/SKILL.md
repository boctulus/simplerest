---
name: multi-tenant-config
description: Guide for configuring multiple database connections and multi-tenant setups in SimpleRest, including connection switching, table prefixes, and schema directories.
---

# Multi-Tenant Configuration Skill

## Architecture

SimpleRest supports multi-tenancy via two mechanisms:
1. **Multiple connections** — separate databases per tenant
2. **Table prefixes** — shared database with prefixed tables

## Step 1: Configure Databases

Edit `config/databases.php`:

```php
<?php

return [
    'main' => [
        'driver'   => 'mysql',
        'host'     => env('DB_HOST'),
        'port'     => env('DB_PORT'),
        'database' => env('DB_DATABASE'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
        'charset'  => 'utf8mb4',
        'schema'   => null,
        'tb_prefix' => '',          // table prefix (e.g., 'wp_' for WordPress)
    ],
    'tenant_alpha' => [
        'driver'   => 'mysql',
        'host'     => env('DB_HOST_TENANT_ALPHA'),
        'database' => env('DB_DATABASE_TENANT_ALPHA'),
        'username' => env('DB_USERNAME_TENANT_ALPHA'),
        'password' => env('DB_PASSWORD_TENANT_ALPHA'),
        'tb_prefix' => 'alpha_',    // table prefix variant
    ],
    'zippy' => [
        'driver'   => 'mysql',
        'host'     => env('DB_HOST_ZIPPY'),
        'database' => env('DB_DATABASE_ZIPPY'),
        'username' => env('DB_USERNAME_ZIPPY'),
        'password' => env('DB_PASSWORD_ZIPPY'),
        'tb_prefix' => 'zippy_',
    ],
    'test_sqlite' => [
        'driver'   => 'sqlite',
        'database' => ':memory:',   // in-memory SQLite for testing
    ],
];
```

## Step 2: Connection Switching

```php
// One-time switch
DB::getConnection('zippy');
$rows = DB::table('products')->get();
DB::closeConnection('zippy');

// Scoped switch (auto-restores previous connection)
withDefaultConnection(function() {
    $users = DB::table('users')->get();  // uses 'zippy'
}, 'zippy');

// Direct on specific connection (no global switch)
$products = DB::connection('tenant_alpha')
    ->table('products')
    ->get();
```

## Step 3: Dynamic Tenant Connection

```php
// Determine tenant from request
$tenantId = Request::getInstance()->header('X-Tenant-ID') ?? 'default';

// Dynamic connection name pattern
$connName = 'tenant_' . $tenantId;
DB::getConnection($connName);

// Or configure on-the-fly
DB::addConnection([
    'driver'   => 'mysql',
    'host'     => env('DB_HOST'),
    'database' => 'db_' . $tenantId,
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'tb_prefix' => $tenantId . '_',
], 'tenant_dynamic_' . $tenantId);
```

## Step 4: Schema Files Per Connection

Schemas are organized by connection:

```
app/Schemas/
├── main/           # schemas for 'main' connection
│   ├── users.php
│   └── products.php
├── zippy/          # schemas for 'zippy' connection
│   ├── users.php
│   └── orders.php
└── edu/            # schemas for 'edu' connection
    ├── courses.php
    └── students.php
```

### Generate Schema for Specific Connection

```bash
php com make schema products          # uses default connection
php com make schema products --from=zippy   # introspects zippy DB
```

## Step 5: Table Prefixes Usage

```php
// config/databases.php
'woo3' => [
    'host'      => env('DB_HOST_WOO3'),
    'database'  => env('DB_NAME_WOO3'),
    'tb_prefix' => 'wp_',    // <--- prefix
];

// Usage
DB::getConnection('woo3');
echo tb_prefix();  // 'wp_'

// Queries use prefix automatically
$rows = table('users')->first();  // SELECT * FROM wp_users ...

// DB::statement() auto-prepends prefix for CREATE/ALTER/INSERT
DB::statement("CREATE TABLE products (id INT AUTO_INCREMENT PRIMARY KEY)");
// → CREATE TABLE wp_products (...)
```

## Supported Database Engines

| Engine | driver value |
|--------|-------------|
| MySQL | `mysql` |
| PostgreSQL | `pgsql` |
| SQLite | `sqlite` |
| SQL Server | `sqlsrv` |
| Oracle | `oracle` |
| Firebird | `firebird` |
| DB2 | `db2` |
| Informix | `informix` |
| Sybase | `sybase` |

## Get Current State

```php
DB::getCurrentConnectionId();  // e.g., 'main', 'zippy'
DB::driver();                   // e.g., 'mysql'
DB::getDefaultConnectionId();   // default connection name
```

## Common Pitfalls

1. **Schema not found** — ensure schema file is in the correct subdirectory matching the connection ID
2. **Table prefix not applied** — verify `tb_prefix` is set AND connection is active
3. **Cross-connection queries** — you CAN'T JOIN across connections. Use separate queries
4. **Connection leak** — always `closeConnection()` after ad-hoc usage
5. **`DB::statement()` prefix** — only applies to CREATE/ALTER/INSERT, not SELECT
6. **Prefix with `table()` helper** — works automatically when connection is set

## Best Practices

1. **Name connections semantically** — `main`, `tenant_X`, `logs`, `analytics`
2. **Use `withDefaultConnection()`** instead of manual connection switching
3. **Close connections** when done with `DB::closeConnection('id')`
4. **Keep schemas organized** by connection directory
5. **Use env vars** for all connection credentials — never hardcode
6. **Test with SQLite** in-memory for unit tests (`:memory:`)
7. **Dynamic tenant connections** should be cached or pooled to avoid reconnection overhead
