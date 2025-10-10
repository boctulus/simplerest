# myModule

Package **myModule** by SimpleREST Team.

Este package fue convertido desde un módulo de SimpleREST y mantiene toda su funcionalidad original.

## Instalación

### Opción 1: Repositorio Local (Recomendado para desarrollo)

1. Agregar el repositorio en `composer.json` (raíz del proyecto):

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/simplerest-team/mymodule",
        "options": {
            "symlink": true
        }
    }
],
"require": {
    "simplerest-team/mymodule": "@dev"
}
```

2. Actualizar Composer:

```bash
composer clear-cache
composer update
composer dump-autoload -o
```

### Opción 2: Agregar Manualmente el Autoload

1. Agregar el mapeo PSR-4 en `composer.json` (raíz):

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "SimplerestTeam\Mymodule\\": "packages/simplerest-team/mymodule/src/"
    }
}
```

2. Regenerar el autoloader:

```bash
composer dump-autoload --no-ansi
```

## Configuración

Agregar el ServiceProvider en `config/config.php`:

```php
'providers' => [
    SimplerestTeam\Mymodule\ServiceProvider::class,
    // ... otros providers
],
```

## Estructura

```
mymodule/
├── assets/          # CSS, JS, images
├── config/          # Configuration files (routes, etc.)
├── database/        # Migrations and seeders
├── etc/             # Additional resources
├── src/             # Source code
│   ├── Controllers/ # Controllers
│   ├── Models/      # Models
│   ├── Middlewares/ # Middlewares
│   ├── Helpers/     # Helper functions
│   ├── Libs/        # Libraries
│   ├── Interfaces/  # Interfaces
│   └── Traits/      # Traits
├── tests/           # Unit tests
├── views/           # View templates
├── composer.json    # Package metadata
├── README.md        # This file
└── LICENSE          # MIT License
```

## Migraciones

Para ejecutar las migraciones de este package:

```bash
php com migrate --dir=packages/simplerest-team/mymodule/database/migrations
```

## Uso

Este package mantiene toda la funcionalidad del módulo original. Consulta la documentación específica del módulo para más detalles sobre su uso.

## Licencia

MIT License - Ver archivo LICENSE para más detalles.
