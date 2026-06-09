# Multi-Database y Multi-Tenant

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

- [`QueryBuilder.md`](./QueryBuilder.md) — todas las operaciones de BD
- [`Schemas.md`](./Schemas.md) — schemas por conexión
- [`config/databases.php`](../config/databases.php)
