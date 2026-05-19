<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\Libs\Files;
use Boctulus\Simplerest\Core\Libs\StdOut;
use Boctulus\Simplerest\Core\Libs\Strings;

class ToPackageCommand extends BaseCommand
{
    public function __construct()
    {
        $this->command     = 'to-package';
        $this->description = 'Convierte un módulo existente en un package distribuible';
        $this->aliases     = ['convert', 'export'];
        $this->examples    = [
            'php com module to-package --name=myModule --author="John Doe"',
            'php com module to-package --name=myModule --author="John Doe" --keep-module',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['name', 'author'],
            'optional' => [],
            'flags'    => ['keep-module'],
            'options'  => [
                'name'        => ['describe' => 'Nombre del módulo a convertir'],
                'author'      => ['describe' => 'Nombre del autor del package'],
                'keep-module' => ['describe' => 'Mantener el módulo original después de la conversión'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $moduleName = $this->opt($parsed, 'name');
        $author     = $this->opt($parsed, 'author');
        $keepModule = $this->opt($parsed, 'keep_module', false);

        $moduleNamePascal = Strings::toPascalCase($moduleName);
        $authorSlug       = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $author));
        $packageSlug      = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $moduleName));

        $modulePath  = MODULES_PATH . $moduleName;
        $packagePath = PACKAGES_PATH . "{$authorSlug}/{$packageSlug}/";

        if (!is_dir($modulePath)) {
            echo "✗ El módulo '{$moduleName}' no existe en: {$modulePath}\n";
            return;
        }

        if (is_dir($packagePath)) {
            echo "✗ El package ya existe en: {$packagePath}\n";
            return;
        }

        StdOut::print("=== Convirtiendo módulo '{$moduleName}' en package ===\n");

        Files::mkDirOrFail($packagePath);
        StdOut::print("✓ Directorio del package creado: {$packagePath}");

        Files::copy($modulePath, $packagePath, ['glob:*']);
        StdOut::print("✓ Archivos del módulo copiados al package");

        $namespace = ucfirst(Strings::toPascalCase($authorSlug)) . '\\' . ucfirst(Strings::toPascalCase($packageSlug));

        $this->generateComposerJson($packagePath, $packageSlug, $authorSlug, $namespace, $moduleName, $author);
        StdOut::print("✓ composer.json generado");

        $this->generateServiceProvider($packagePath, $namespace, $moduleNamePascal);
        StdOut::print("✓ ServiceProvider generado");

        $this->generateReadme($packagePath, $moduleName, $author, $namespace, $packageSlug, $authorSlug);
        StdOut::print("✓ README.md generado");

        $this->generateLicense($packagePath, $author);
        StdOut::print("✓ LICENSE generado");

        $this->adjustNamespaces($packagePath, $namespace, $moduleName);
        StdOut::print("✓ Namespaces ajustados");

        $mainFile = $packagePath . 'Main.php';
        if (file_exists($mainFile)) {
            unlink($mainFile);
            StdOut::print("✓ Main.php eliminado");
        }

        if (!$keepModule) {
            Files::rmDirOrFail($modulePath, true);
            StdOut::print("✓ Módulo original eliminado de: {$modulePath}");
        } else {
            StdOut::print("⚠ Módulo original mantenido en: {$modulePath}");
        }

        StdOut::print("\n=== ✓ Conversión completada ===\n");
        $this->showInstallInstructions($namespace, $packageSlug, $authorSlug);
    }

    private function generateComposerJson(string $path, string $pkgSlug, string $authorSlug, string $ns, string $moduleName, string $author): void
    {
        $data = [
            'name'        => "{$authorSlug}/{$pkgSlug}",
            'description' => "Package {$moduleName} (converted from module) by {$author}",
            'type'        => 'library',
            'license'     => 'MIT',
            'authors'     => [['name' => $author, 'email' => 'author@example.com']],
            'autoload'    => ['psr-4' => ["{$ns}\\" => 'src/']],
            'extra'       => ['simplerest' => ['providers' => ["{$ns}\\ServiceProvider"]]],
            'require'     => new \stdClass(),
        ];
        file_put_contents($path . 'composer.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function generateServiceProvider(string $path, string $ns, string $moduleName): void
    {
        $content = <<<PHP
<?php

namespace {$ns};

use Boctulus\Simplerest\Core\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        \$routesFile = __DIR__ . '/../config/routes.php';
        if (file_exists(\$routesFile)) {
            require_once \$routesFile;
        }
    }

    public function boot() {}
}
PHP;
        @mkdir($path . 'src', 0755, true);
        file_put_contents($path . 'src/ServiceProvider.php', $content);
    }

    private function generateReadme(string $path, string $moduleName, string $author, string $ns, string $pkgSlug, string $authorSlug): void
    {
        $content = "# {$moduleName}\n\nPackage **{$moduleName}** by {$author}.\n\nConvertido desde módulo SimpleREST.\n";
        file_put_contents($path . 'README.md', $content);
    }

    private function generateLicense(string $path, string $author): void
    {
        $year    = date('Y');
        $content = "MIT License\n\nCopyright (c) {$year} {$author}\n";
        file_put_contents($path . 'LICENSE', $content);
    }

    private function adjustNamespaces(string $path, string $newNs, string $moduleName): void
    {
        $moduleNamePascal = Strings::toPascalCase($moduleName);
        $oldNs            = "Boctulus\\Simplerest\\modules\\{$moduleNamePascal}";

        foreach (Files::recursiveGlob($path . '*.php') as $file) {
            $content = file_get_contents($file);
            $updated = str_replace($oldNs, $newNs, $content);
            if ($updated !== $content) {
                file_put_contents($file, $updated);
            }
        }
    }

    private function showInstallInstructions(string $ns, string $pkgSlug, string $authorSlug): void
    {
        StdOut::print("=== Instrucciones de instalación ===\n");
        StdOut::print("1. Agrega al autoload en composer.json:");
        StdOut::print("   \"{$ns}\\\\\": \"packages/{$authorSlug}/{$pkgSlug}/src/\"");
        StdOut::print("\n2. Registra el ServiceProvider en config/config.php:");
        StdOut::print("   '{$ns}\\ServiceProvider::class'");
        StdOut::print("\n3. Regenera el autoloader:");
        StdOut::print("   composer dump-autoload --no-ansi\n");
    }
}
