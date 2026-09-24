# Guía de Migración v0.9 → v1.0

## Cambios Principales

La versión 1.0 introdujo una reorganización arquitectónica significativa (BC break). Los cambios clave:

### 1. Reorganización de Directorios

```
Antes (v0.9):                    Después (v1.0):
app/Core/                      → src/framework/
  ├── Api/                     → src/framework/Api/
  ├── Controllers/             → src/framework/Controllers/
  ├── Handlers/                → src/framework/Handlers/
  ├── Libs/                    → src/framework/Libs/
  ├── Helpers/                 → src/framework/Helpers/
  ├── Traits/                  → src/framework/Traits/
  ├── Interfaces/              → src/framework/Interfaces/
  ├── Exceptions/              → src/framework/Exceptions/
  ├── Security/                → src/framework/Security/
  └── Psr7/                    → src/framework/Psr7/

app/Middlewares/               → app/Middlewares/
app/Commands/                  → app/Commands/
app/Controllers/               → app/Controllers/
app/Models/                    → app/Models/
```

### 2. Namespaces Actualizados

| v0.9 | v1.0 |
|------|------|
| `Boctulus\Simplerest\Core\*` | `Boctulus\Simplerest\*` |
| `Boctulus\Simplerest\Core\Libs\DB` | `Boctulus\Simplerest\Libs\DB` |
| `Boctulus\Simplerest\Core\Api\ApiController` | `Boctulus\Simplerest\Api\ApiController` |

### 3. Cambios en Composer

```json
{
  "autoload": {
    "psr-4": {
      "Boctulus\\Simplerest\\": "src/framework/"
    }
  }
}
```

### 4. FrontController → Pipeline de Handlers

El FrontController ahora usa 6 handlers plugueables en vez de lógica monolítica:

```php
// config/config.php
'behaviors' => [
    'handlers' => [
        RequestHandler::class,
        ApiHandler::class,
        AuthHandler::class,
        OutputHandler::class,
        MiddlewareHandler::class,
        ErrorHandler::class,
    ]
]
```

### 5. ACL v4 (Compiled Permissions)

El sistema ACL fue reescrito con permisos compilados y denegación explícita. Ya no se evalúan permisos en tiempo real contra la BD para cada request.

Ver [`CHANGELOG-acl.md`](./CHANGELOG-acl.md).

### 6. Métodos Inmutables

Request y Response ahora soportan métodos `with*()` inmutables (PSR-7 compliance):

```php
$newResponse = $response->withHeader('X-Custom', 'value');
// $response original no se modifica
```

---

## Pasos para Migrar

1. Actualizar imports de `Boctulus\Simplerest\Core\*` → `Boctulus\Simplerest\*`
2. Mover código de `app/Core/` → `src/framework/` si es framework, o a `app/` si es aplicación
3. Actualizar `composer.json` con nuevo autoload
4. Ejecutar `composer dump-autoload`
5. Revisar configuración ACL (migrar a formato v4)
6. Verificar rutas y middlewares

---

## Ver También

- [`CHANGELOG.md`](./CHANGELOG.md)
- [`Framework-Architecture.md`](./Framework-Architecture.md)
