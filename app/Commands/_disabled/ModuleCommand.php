<?php

use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\StdOut;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Interfaces\ICommand;
use Boctulus\Simplerest\Core\Traits\CommandTrait;

/**
 * Module Command
 *
 * Comandos para gestionar módulos en SimpleREST
 */
class ModuleCommand implements ICommand
{
    use CommandTrait;

    /**
     * Convierte un módulo en un package
     *
     * @param string $moduleName Nombre del módulo a convertir
     * @param mixed ...$options Opciones adicionales
     */
    public function toPackage(string $moduleName, ...$options)
    {
        $opts = $this->parseOptions($options);

        // Validar que se proporcionó el autor
        if (empty($opts['author'])) {
            StdOut::print("Error: Se requiere especificar el autor con --author=<nombre>");
            StdOut::print("Uso: php com module --to_package=<moduleName> --author=<author> [--keep-module]");
            return;
        }

        $author = $opts['author'];
        $keepModule = $opts['keep_module'] ?? false;

        // Normalizar nombres
        $moduleNamePascal = Strings::toPascalCase($moduleName);
        $authorSlug = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9]+/', '-', $author)));
        $packageSlug = strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9]+/', '-', $moduleName)));

        // Validar que el módulo existe
        $modulePath = MODULES_PATH . $moduleName;

        if (!is_dir($modulePath)) {
            StdOut::print("Error: El módulo '$moduleName' no existe en: $modulePath");
            return;
        }

        StdOut::print("=== Convirtiendo módulo '$moduleName' en package ===\n");

        // Crear la ruta del package
        $packagePath = PACKAGES_PATH . "{$authorSlug}/{$packageSlug}/";

        // Verificar si el package ya existe
        if (is_dir($packagePath)) {
            StdOut::print("Error: El package ya existe en: $packagePath");
            StdOut::print("Por favor, elimínelo primero o use un nombre diferente.");
            return;
        }

        // Crear el directorio del package
        Files::mkDirOrFail($packagePath);
        StdOut::print("✓ Creado directorio del package: $packagePath");

        // Copiar todos los archivos del módulo al package
        $this->copyModuleToPackage($modulePath, $packagePath, $moduleName);
        StdOut::print("✓ Archivos del módulo copiados al package");

        // Generar namespace del package
        $namespace = ucfirst(Strings::toPascalCase($authorSlug)) . "\\" . ucfirst(Strings::toPascalCase($packageSlug));

        // Generar composer.json
        $this->generateComposerJson($packagePath, $packageSlug, $authorSlug, $namespace, $moduleName, $author);
        StdOut::print("✓ composer.json generado");

        // Generar ServiceProvider
        $this->generateServiceProvider($packagePath, $namespace, $moduleNamePascal);
        StdOut::print("✓ ServiceProvider generado");

        // Generar README.md
        $this->generateReadme($packagePath, $moduleName, $author, $namespace, $packageSlug, $authorSlug);
        StdOut::print("✓ README.md generado");

        // Generar LICENSE
        $this->generateLicense($packagePath, $author);
        StdOut::print("✓ LICENSE generado");

        // Ajustar namespaces en todos los archivos PHP
        $this->adjustNamespaces($packagePath, $namespace, $moduleName);
        StdOut::print("✓ Namespaces ajustados en archivos PHP");

        // Eliminar el archivo Main.php si existe (ya no es necesario en package)
        $mainFile = $packagePath . 'Main.php';
        if (file_exists($mainFile)) {
            unlink($mainFile);
            StdOut::print("✓ Archivo Main.php eliminado (no necesario en packages)");
        }

        // Eliminar el módulo original si se especificó
        if (!$keepModule) {
            Files::rmDirOrFail($modulePath, true);
            StdOut::print("✓ Módulo original eliminado de: $modulePath");
        } else {
            StdOut::print("⚠ Módulo original mantenido en: $modulePath");
        }

        StdOut::print("\n=== ✓ Conversión completada con éxito ===\n");

        // Mostrar instrucciones de instalación
        $this->showInstallationInstructions($namespace, $packageSlug, $authorSlug);
    }

    /**
     * Copia todo el contenido del módulo al package
     */
    protected function copyModuleToPackage(string $modulePath, string $packagePath, string $moduleName)
    {
        // Usar Files::copy() para copiar recursivamente todo el contenido
        Files::copy($modulePath, $packagePath, ['glob:*']);
    }

    /**
     * Genera el archivo composer.json
     */
    protected function generateComposerJson(string $packagePath, string $packageSlug, string $authorSlug, string $namespace, string $moduleName, string $author)
    {
        $composerJsonPath = $packagePath . 'composer.json';

        $composerContent = [
            "name" => "{$authorSlug}/{$packageSlug}",
            "description" => "Package {$moduleName} (converted from module) by {$author}",
            "type" => "library",
            "keywords" => ["simplerest", "module", "package", $packageSlug],
            "license" => "MIT",
            "authors" => [
                [
                    "name" => $author,
                    "email" => "author@example.com"
                ]
            ],
            "autoload" => [
                "psr-4" => [
                    $namespace . "\\" => "src/"
                ]
            ],
            "extra" => [
                "simplerest" => [
                    "providers" => [
                        $namespace . "\\ServiceProvider"
                    ]
                ]
            ],
            "require" => new stdClass()
        ];

        file_put_contents($composerJsonPath, json_encode($composerContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Genera el ServiceProvider
     */
    protected function generateServiceProvider(string $packagePath, string $namespace, string $moduleName)
    {
        $serviceProviderPath = $packagePath . 'src/ServiceProvider.php';

        $content = <<<PHP
<?php

namespace {$namespace};

use Boctulus\Simplerest\Core\ServiceProvider as BaseServiceProvider;

/**
 * Service Provider for {$moduleName}
 *
 * Este archivo fue generado automáticamente durante la conversión de módulo a package.
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Registrar servicios, cargar configuración, etc.

        // Cargar rutas si existen
        \$routesFile = __DIR__ . '/../config/routes.php';
        if (file_exists(\$routesFile)) {
            require_once \$routesFile;
        }
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Código que se ejecuta al inicio de la aplicación

        // Cargar vistas, publicar assets, etc.
    }
}

PHP;

        file_put_contents($serviceProviderPath, $content);
    }

    /**
     * Genera el README.md
     */
    protected function generateReadme(string $packagePath, string $moduleName, string $author, string $namespace, string $packageSlug, string $authorSlug)
    {
        $readmePath = $packagePath . 'README.md';

        $content = <<<MD
# {$moduleName}

Package **{$moduleName}** by {$author}.

Este package fue convertido desde un módulo de SimpleREST y mantiene toda su funcionalidad original.

## Instalación

### Opción 1: Repositorio Local (Recomendado para desarrollo)

1. Agregar el repositorio en `composer.json` (raíz del proyecto):

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/{$authorSlug}/{$packageSlug}",
        "options": {
            "symlink": true
        }
    }
],
"require": {
    "{$authorSlug}/{$packageSlug}": "@dev"
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
        "App\\\\": "app/",
        "{$namespace}\\\\": "packages/{$authorSlug}/{$packageSlug}/src/"
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
    {$namespace}\\ServiceProvider::class,
    // ... otros providers
],
```

## Estructura

```
{$packageSlug}/
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
php com migrate --dir=packages/{$authorSlug}/{$packageSlug}/database/migrations
```

## Uso

Este package mantiene toda la funcionalidad del módulo original. Consulta la documentación específica del módulo para más detalles sobre su uso.

## Licencia

MIT License - Ver archivo LICENSE para más detalles.

MD;

        file_put_contents($readmePath, $content);
    }

    /**
     * Genera el archivo LICENSE (MIT)
     */
    protected function generateLicense(string $packagePath, string $author)
    {
        $licensePath = $packagePath . 'LICENSE';
        $year = date('Y');

        $content = <<<LICENSE
MIT License

Copyright (c) {$year} {$author}

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

LICENSE;

        file_put_contents($licensePath, $content);
    }

    /**
     * Ajusta los namespaces en todos los archivos PHP del package
     */
    protected function adjustNamespaces(string $packagePath, string $newNamespace, string $moduleName)
    {
        $moduleNamePascal = Strings::toPascalCase($moduleName);
        $oldNamespace = "Boctulus\\Simplerest\\modules\\{$moduleNamePascal}";

        // Buscar todos los archivos PHP recursivamente
        $phpFiles = Files::recursiveGlob($packagePath . '*.php');

        foreach ($phpFiles as $file) {
            $content = file_get_contents($file);

            if ($content === false) {
                continue;
            }

            // Reemplazar el namespace antiguo por el nuevo
            $updatedContent = str_replace($oldNamespace, $newNamespace, $content);

            // También actualizar referencias en use statements
            $updatedContent = preg_replace(
                '/use\s+Boctulus\\\\Simplerest\\\\modules\\\\' . $moduleNamePascal . '\\\\/',
                'use ' . str_replace('\\', '\\\\', $newNamespace) . '\\',
                $updatedContent
            );

            // Guardar solo si hubo cambios
            if ($updatedContent !== $content) {
                file_put_contents($file, $updatedContent);
            }
        }
    }

    /**
     * Muestra las instrucciones de instalación al usuario
     */
    protected function showInstallationInstructions(string $namespace, string $packageSlug, string $authorSlug)
    {
        StdOut::print("=== Instrucciones de Instalación ===\n");
        StdOut::print("1. Agrega el package a tu composer.json (raíz del proyecto):");
        StdOut::print("");
        StdOut::print("   \"autoload\": {");
        StdOut::print("       \"psr-4\": {");
        StdOut::print("           \"App\\\\\": \"app/\",");
        StdOut::print("           \"{$namespace}\\\\\": \"packages/{$authorSlug}/{$packageSlug}/src/\"");
        StdOut::print("       }");
        StdOut::print("   }");
        StdOut::print("");
        StdOut::print("2. Registra el ServiceProvider en config/config.php:");
        StdOut::print("");
        StdOut::print("   'providers' => [");
        StdOut::print("       {$namespace}\\ServiceProvider::class,");
        StdOut::print("       // ...");
        StdOut::print("   ],");
        StdOut::print("");
        StdOut::print("3. Regenera el autoloader:");
        StdOut::print("");
        StdOut::print("   composer dump-autoload --no-ansi");
        StdOut::print("");
        StdOut::print("4. (Opcional) Ejecuta las migraciones si existen:");
        StdOut::print("");
        StdOut::print("   php com migrate --dir=packages/{$authorSlug}/{$packageSlug}/database/migrations");
        StdOut::print("");
    }

    /**
     * Parsea las opciones pasadas al comando
     */
    protected function parseOptions(array $args): array
    {
        $options = [
            'author' => '',
            'keep_module' => false,
        ];

        foreach ($args as $arg) {
            if (preg_match('/^--author[=:](.+)$/', $arg, $matches)) {
                $options['author'] = trim($matches[1]);
            } elseif ($arg === '--keep-module' || $arg === '-k') {
                $options['keep_module'] = true;
            }
        }

        return $options;
    }

    /**
     * Muestra la ayuda del comando
     */
    public function help($name = null, ...$args)
    {
        echo "Module Command - Gestión de módulos en SimpleREST\n\n";
        echo "Uso:\n";
        echo "  php com module --to_package=<moduleName> --author=<author> [opciones]\n\n";
        echo "Opciones:\n";
        echo "  --author=<nombre>     Nombre del autor del package (requerido)\n";
        echo "  --keep-module, -k     Mantener el módulo original después de la conversión\n\n";
        echo "Ejemplos:\n\n";
        echo "  # Convertir módulo a package\n";
        echo "  php com module --to_package=myModule --author=\"John Doe\"\n\n";
        echo "  # Convertir y mantener el módulo original\n";
        echo "  php com module --to_package=myModule --author=\"John Doe\" --keep-module\n\n";
        echo "Descripción:\n";
        echo "  Este comando convierte un módulo existente en un package distribuible.\n";
        echo "  El proceso incluye:\n";
        echo "    - Copiar todos los archivos del módulo al package\n";
        echo "    - Generar composer.json con PSR-4 autoloading\n";
        echo "    - Crear ServiceProvider para el package\n";
        echo "    - Generar README.md con instrucciones de instalación\n";
        echo "    - Crear archivo LICENSE (MIT)\n";
        echo "    - Ajustar todos los namespaces automáticamente\n";
        echo "    - Eliminar el módulo original (a menos que se use --keep-module)\n\n";
    }
}
