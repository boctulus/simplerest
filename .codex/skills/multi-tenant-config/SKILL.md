---
name: multi-tenant-config
description: Guide for configuring multiple database connections and multi-tenant setups in SimpleRest, including connection switching, table prefixes, and schema directories.
---

# Multi-Tenant Configuration Skill

## Architecture

Two approaches: separate databases per tenant OR shared DB with table prefixes.

## 1. Configure Databases (`config/databases.php`)

```php
return [
    'main' => [
        'driver'    => 'mysql',
        'host'      => env('DB_HOST'),
        'database'  => env('DB_DATABASE'),
        'username'  => env('DB_USERNAME'),
        'password'  => env('DB_PASSWORD'),
        'tb_prefix' => '',
    ],
    'tenant_alpha' => [
        'driver'    => 'mysql',
        'host'      => env('DB_HOST_TENANT_ALPHA'),
        'database'  => env('DB_DATABASE_TENANT_ALPHA'),
        'username'  => env('DB_USERNAME_TENANT_ALPHA'),
        'password'  => env('DB_PASSWORD_TENANT_ALPHA'),
        'tb_prefix' => 'alpha_',
    ],
];
```

## 2. Connection Switching

```php
DB::getConnection('zippy');                                    // global switch
DB::connection('tenant_alpha')->table('users')->get();         // scoped
withDefaultConnection(function() { DB::table('users')->get(); }, 'zippy');  // auto-restore
DB::closeConnection('zippy');                                  // cleanup
```

## 3. Dynamic Tenant Connections

```php
$tenantId = Request::getInstance()->header('X-Tenant-ID') ?? 'default';
DB::addConnection([
    'driver'    => 'mysql',
    'database'  => 'db_' . $tenantId,
    'username'  => env('DB_USERNAME'),
    'password'  => env('DB_PASSWORD'),
    'tb_prefix' => $tenantId . '_',
], 'tenant_' . $tenantId);
DB::getConnection('tenant_' . $tenantId);
```

## 4. Schema Files Per Connection

```
app/Schemas/main/      -> main connection
app/Schemas/zippy/     -> zippy connection
```

```bash
php com make schema products                    # default connection
php com make schema products --from=zippy       # specific connection
```

## 5. Table Prefixes

```php
// config: 'tb_prefix' => 'wp_'
DB::getConnection('woo3');
echo tb_prefix();                                # 'wp_'
table('users')->first();                         # SELECT * FROM wp_users
// DB::statement() auto-prepends prefix for CREATE/ALTER/INSERT
```

## Supported Engines

`mysql`, `pgsql`, `sqlite`, `sqlsrv`, `oracle`, `firebird`, `db2`, `informix`, `sybase`

## State Methods

```php
DB::getCurrentConnectionId();
DB::getDefaultConnectionId();
DB::driver();
```

## Pitfalls

1. Schema not found -> check subdirectory matches connection ID
2. Prefix not applied -> verify `tb_prefix` is set and connection active
3. Cant JOIN across connections (use separate queries)
4. Close ad-hoc connections after use
5. `DB::statement()` prefix only applies to CREATE/ALTER/INSERT
