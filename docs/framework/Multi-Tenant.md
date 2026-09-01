# Multi-Database y Multi-Tenant

## Multi-Database

SimpleRest soporta múltiples conexiones a base de datos simultáneas.

**Configuración**: `config/databases.php`

```php
return [
    'db_connection_default' => 'main',

    'db_connections' => [
        'main' => [
            'driver'  => 'mysql',
            'host'    => env('DB_HOST'),
            'port'    => env('DB_PORT'),
            'db_name' => env('DB_NAME'),
            'user'    => env('DB_USERNAME'),
            'pass'    => env('DB_PASSWORD'),
        ],
        'zippy' => [
            'driver'  => 'mysql',
            'host'    => env('DB_HOST_ZIPPY'),
            'db_name' => env('DB_NAME_ZIPPY'),
            'user'    => env('DB_USERNAME_ZIPPY'),
            'pass'    => env('DB_PASSWORD_ZIPPY'),
        ],
        'test_sqlite' => [
            'driver'  => 'sqlite',
            'db_name' => ':memory:',
        ],
    ],

    // Historical spelling used by the framework API. Keep this key even when
    // the application does not define tenant groups.
    'tentant_groups' => [],
];
```

`Config::get()` merges `config/config.php` and `config/databases.php`. Database-specific settings such as `db_connections`, `db_connection_default` and `tentant_groups` can therefore live in `config/databases.php`.

## Conexión default y conexiones no-default

`db_connection_default` identifica la conexión principal de la aplicación. Normalmente es `main`.

La resolución de schemas distingue explícitamente entre la conexión default y cualquier conexión secundaria:

```text
main == db_connection_default
    -> app/Schemas/main/
    -> no consulta tenant groups

zippy != db_connection_default
    -> getTenantGroupName('zippy')
    -> app/Schemas/zippy/ si no pertenece a un grupo

test_sqlite != db_connection_default
    -> getTenantGroupName('test_sqlite')
    -> app/Schemas/test_sqlite/ si no pertenece a un grupo
```

Esto importa aunque la aplicación no use multi-tenancy. Algunas operaciones de `DB` consultan metadata del schema después de ejecutar SQL. Cuando la conexión activa no es la default, esa resolución pasa por `DB::getTenantGroupName()`.

Por ese motivo, **la clave `tentant_groups` debe existir siempre**, aunque esté vacía:

```php
'tentant_groups' => [],
```

> `tentant_groups` contiene una errata histórica (`tentant`, no `tenant`) que actualmente forma parte del contrato de configuración del framework. No debe corregirse de forma aislada en una aplicación.

Si la clave no existe, una operación que funciona sobre `main` puede fallar sobre una conexión secundaria como `test_sqlite` al intentar resolver el namespace/directorio de schemas.

## Tenant groups

Los tenant groups permiten que múltiples conexiones compartan el mismo conjunto de schemas y modelos. Se configuran como un mapa `group_name => patrones de connection id`:

```php
return [
    'db_connection_default' => 'main',

    'db_connections' => [
        // ...
    ],

    'tentant_groups' => [
        'companies' => [
            'company_db-[0-9]+',
            'company_testing',
        ],
        'legion' => [
            'db_[0-9]+',
            'db_legion',
            'db_flor',
        ],
    ],
];
```

Cuando una conexión pertenece a un grupo, SimpleRest utiliza el nombre del grupo para resolver schemas y modelos compartidos. Cuando no pertenece a ninguno, utiliza el identificador de la conexión.

```text
company_db-42
    -> tenant group companies
    -> app/Schemas/companies/

zippy
    -> sin tenant group
    -> app/Schemas/zippy/
```

La conexión default es un caso especial: sus schemas se resuelven directamente bajo `app/Schemas/{db_connection_default}/` sin consultar `tentant_groups`.

## Selección de conexión

La API de bajo nivel permite seleccionar una conexión concreta:

```php
DB::getConnection('zippy');
```

Las operaciones raw de `DB` aceptan opcionalmente un `tenant_id`/connection id y restauran la conexión previa al finalizar.

```php
$rows = DB::select(
    'SELECT * FROM products WHERE active = ?',
    [1],
    'ASSOC',
    'zippy'
);
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

SimpleRest soporta multi-tenancy mediante conexiones separadas, tenant groups y, cuando corresponde, prefijos de tabla.

Los schemas se organizan por conexión o por tenant group:

```text
app/Schemas/
├── main/          # conexión default
├── zippy/         # conexión no-default sin grupo
├── companies/     # schemas compartidos por un tenant group
└── test_sqlite/   # conexión de test no-default
```

La relación entre conexión y directorio de schemas forma parte del mecanismo de resolución de `DBRels`. Por eso cualquier conexión secundaria utilizada por tests o tooling necesita una configuración compatible aunque la aplicación no sea multi-tenant.

## Ver También

- [`QueryBuilder.md`](./QueryBuilder.md) — operaciones de BD y Query Builder
- [`Schemas.md`](./Schemas.md) — schemas por conexión
- [`config/databases.php`](../../config/databases.php) — configuración real de conexiones
