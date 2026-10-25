---
name: multi-tenant-config
description: Guide to SimpleRest's named database connections, tenant-aware schema paths and table prefixes.
---

# Multi-Tenant Configuration Skill

## Architecture

SimpleRest supports multiple named connections and configurable table prefixes. Tenant identity, authorization and isolation must be enforced by the application; do not select a database directly from an untrusted request header.

## 1. Configure Databases (`config/databases.php`)

```php
return [
    'db_connection_default' => 'main',
    'db_connections' => [
        'main' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'db_name' => env('DB_NAME'),
            'user' => env('DB_USERNAME'),
            'pass' => env('DB_PASSWORD'),
            'tb_prefix' => '',
        ],
        'tenant_alpha' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_TENANT_ALPHA'),
            'db_name' => env('DB_NAME_TENANT_ALPHA'),
            'user' => env('DB_USERNAME_TENANT_ALPHA'),
            'pass' => env('DB_PASSWORD_TENANT_ALPHA'),
            'tb_prefix' => 'alpha_',
        ],
    ],
];
```

The keys are `db_connections`, `db_name`, `user` and `pass`, not Laravel's `database`, `username` or `password`.

## 2. Connection Switching

```php
DB::setConnection('tenant_alpha');
$pdo = DB::getConnection();
$users = DB::table('users')->get();

$users = DB::withConnection('tenant_alpha', function () {
    return DB::table('users')->get();
});

DB::closeConnection('tenant_alpha');
```

`DB::withConnection()` restores the previous selection when one existed. If none was selected before the call, the new selection remains active. Do not close a connection with an active transaction.

`DB::getConnection('tenant_alpha')` also selects that connection and returns its PDO handle. It is **not** a fluent query builder. `DB::connection('tenant_alpha')->table(...)` is not a SimpleRest API.

## 3. Selecting a Tenant Connection

Resolve the tenant in your application's authenticated and authorized context, then map it to a configured connection.

```php
// $authenticatedTenantId is supplied by the application's auth/tenant layer.
$connectionByTenant = ['alpha' => 'tenant_alpha'];
$connectionId = $connectionByTenant[$authenticatedTenantId] ?? null;

if ($connectionId === null || !DB::connectionExists($connectionId)) {
    throw new RuntimeException('Unknown or unauthorized tenant');
}

$users = DB::withConnection($connectionId, function () {
    return DB::table('users')->get();
});
```

`DB::addConnection()` is not part of the current `DB` API. For dynamic tenants, supply trusted connection definitions through application configuration and validate the chosen ID. Never construct database names or prefixes directly from arbitrary client input.

## 4. Schema Files Per Connection

The default connection uses `app/Schemas/<default-connection-id>/`. Other connections resolve schema descriptors using the tenant group if configured; this may differ from the connection ID. Inspect `DBRels::getSchemaPath()` for the lookup rule.

```bash
php com make schema products
php com make schema products --from=tenant_alpha
```

## 5. Table Prefixes

```php
// Assuming tenant_alpha has 'tb_prefix' => 'alpha_'
DB::setConnection('tenant_alpha');
echo tb_prefix();
$users = table('users')->get();
```

Prefix handling depends on the query execution path. Check generated SQL for raw statements rather than assuming every statement is transformed identically.

## Supported Engines

PDO connection branches exist for `mysql`, `sqlite`, `pgsql` and `sqlsrv`. Driver aliases include `mariadb`, `postgres` and `mssql`. Constants for other engines do not establish working adapters.

## State Methods

```php
DB::getCurrentConnectionId();
DB::getDefaultConnectionId();
DB::driver();
```

## Pitfalls

1. Schema not found: check the default connection or tenant-group schema path.
2. Prefix not applied: check `tb_prefix`, active connection and SQL generation path.
3. Joins across distinct database connections are not provided by the Query Builder.
4. Do not close active transaction connections; `withConnection()` restores only an existing prior selection.
5. For raw SQL, verify prefix handling rather than extrapolating from model-backed queries.

See [database connections](../../../docs/database/connections.md), [schema descriptors](../../../docs/database/schemas.md) and [migrations](../../../docs/database/migrations.md).
