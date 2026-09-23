# Multi-Database y Multi-Tenant

> **Audit status:** This is legacy input, not a current connection or tenancy guide. The old `DB::connection(...)` examples and the Oracle/Firebird/DB2/Informix/Sybase driver entries conflict with the current connection API and PDO construction paths. Connection-selection findings are recorded in the [audit register](../../README.md#claim-level-findings-database-connections), with source-level behavior summarized in [Database connections](../../../database/connections.md). Tenant isolation, schema-per-tenant behavior, and prefix-based tenancy remain unverified.

## Multi-Database

SimpleRest soporta múltiples conexiones a base de datos simultáneas.

**Configuración**: `config/databases.php`

```php
return [
    'main' => [
        'driver'   => 'mysql',
        'host'     => env('DB_HOST'),
        'port'     => env('DB_PORT'),
        'database' => env('DB_DATABASE'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
    ],
    'zippy' => [
        'driver'   => 'mysql',
        'host'     => env('DB_HOST_ZIPPY'),
        'database' => env('DB_DATABASE_ZIPPY'),
        'username' => env('DB_USERNAME_ZIPPY'),
        'password' => env('DB_PASSWORD_ZIPPY'),
    ],
    'test_sqlite' => [
        'driver'   => 'sqlite',
        'database' => ':memory:',
    ],
];
```

## Conexiones por Defecto

```php
// Usar conexión específica
DB::connection('zippy')->table('products')->get();

// Establecer conexión por defecto temporal
withDefaultConnection(function() {
    DB::table('users')->get();  // Usa 'zippy'
}, 'zippy');
```

## DB Engines Soportados

| Engine | Driver |
|--------|--------|
| MySQL | `mysql` |
| PostgreSQL | `pgsql` |
| SQLite | `sqlite` |
| SQL Server | `sqlsrv` |
| Oracle | `oracle` |
| Firebird | `firebird` |
| DB2 | `db2` |
| Informix | `informix` |
| Sybase | `sybase` |

## Multi-Tenant

Soporte multi-tenant mediante **prefijos de tabla** y **conexiones separadas**:

```php
// Esquemas por conexión
app/Schemas/main/      → Conexión principal
app/Schemas/zippy/      → Conexión Zippy
app/Schemas/edu/        → Conexión EDU

// Conexión dinámica por tenant
DB::connection('tenant_' . $tenantId)->table('users')->get();
```

## Ver También

- [`QueryBuilder.md`](./QueryBuilder.md) — legacy Query Builder material, pending audit
- [`Schemas.md`](./Schemas.md) — legacy schema material (pending audit)
- [`DB` implementation](../../../../src/framework/Libs/DB.php)
