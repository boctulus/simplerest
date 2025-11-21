# ModuleProvider Documentation

## Overview

A `ModuleProvider` is a specialized service provider that follows the same pattern as package service providers in the SimpleRest framework. It allows modules to register their routes, services, middleware, and other configurations automatically when the application boots.

## Purpose

ModuleProviders serve several important purposes:

1. **Automatic Registration**: They allow modules to automatically register their functionality without manual configuration.
2. **Route Loading**: They provide a mechanism to load module-specific routes when the application starts.
3. **Service Registration**: They can register module services, bindings, and other configurations.
4. **Consistent Architecture**: They follow the same pattern as package ServiceProviders, ensuring consistency across the application.

## Structure

A ModuleProvider class extends the `ServiceProvider` base class and implements two key methods:

### Required Methods

- `register()`: Used to register services in the container (executed during registration phase)
- `boot()`: Used to bootstrap the service after all other services have been registered

### Basic Structure
```php
<?php

namespace Boctulus\Simplerest\Modules\ModuleName;

use Boctulus\Simplerest\Core\ServiceProvider;

class ModuleProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load routes, register middleware, etc.
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register services in container
    }
}
```

## Registration Process

### Step 1: Create the ModuleProvider
Create a `ModuleProvider.php` file in your module's `src` directory:

```
app/
└── Modules/
    └── xeni/
        └── src/
            └── ModuleProvider.php
```

### Step 2: Implement the ModuleProvider
Follow the structure pattern shown above, typically using the `boot()` method to load module routes:

```php
public function boot()
{
    $routesPath = __DIR__ . '/../config/routes.php';
    if (file_exists($routesPath)) {
        include $routesPath;
    }
}
```

### Step 3: Register the Provider
Add the ModuleProvider to the `providers` array in `config/config.php`:

```php
'providers' => [
    // ... other providers
    Boctulus\Simplerest\Modules\Xeni\ModuleProvider::class,
    // ... other providers
],
```

## File Structure Convention

Recommended module structure:

```
app/
└── Modules/
    └── ModuleName/
        ├── assets/
        ├── config/
        │   └── routes.php
        ├── database/
        ├── docs/
        ├── src/
        │   ├── Controllers/
        │   ├── ModuleProvider.php
        │   └── ... other module classes
        ├── tests/
        └── views/
```

## Example Implementation

Here's a complete example of a ModuleProvider for the xeni module:

```php
<?php

namespace Boctulus\Simplerest\Modules\xeni;

use Boctulus\Simplerest\Core\ServiceProvider;

class ModuleProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $routesPath = __DIR__ . '/../config/routes.php';
        if (file_exists($routesPath)) {
            include $routesPath;
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register module services if needed
    }
}
```

## Best Practices

1. **Consistent Naming**: Use `ModuleProvider.php` as the filename
2. **Proper Namespace**: Match the module's directory structure in the namespace
3. **Safe Includes**: Always check if files exist before including them
4. **Path Resolution**: Use `__DIR__` for relative path resolution from the ModuleProvider location
5. **Modular Design**: Keep module-specific logic within the module directory

## Relationship to Package System

ModuleProviders follow the same pattern as package ServiceProviders, ensuring consistency:

- Both extend the `ServiceProvider` base class
- Both implement `register()` and `boot()` methods
- Both are registered in the main configuration
- Both serve to bootstrap module/package functionality

The main difference is that ModuleProviders are for application modules (in `app/Modules/`) while package ServiceProviders are for external packages (in `packages/`).

## Troubleshooting

Common issues and solutions:

1. **Route not found**: Ensure the ModuleProvider is registered in `config/config.php`
2. **Include path errors**: Verify the path in the `boot()` method is correct relative to the ModuleProvider file
3. **Namespace issues**: Confirm the namespace matches the actual file location
4. **File permissions**: Ensure the application has read access to the ModuleProvider and routes files