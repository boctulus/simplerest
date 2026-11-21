<?php

namespace Boctulus\Simplerest\CodeTools\Commands\packages;

use Boctulus\Simplerest\Core\Commands\BaseCommand;

class PackageListCommand extends BaseCommand
{
    public string $group = 'packages';

    public function __construct()
    {
        $this->command = 'list';
        $this->description = 'Lista packages locales y su estado de autoload Composer y comandos CLI';
        $this->examples = [
            'php com packages list',
            'php com packages list --status=active',
            'php com packages list --status=inactive',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['status'],
            'flags' => [],
            'options' => [
                'status' => ['describe' => 'Filtra por comandos CLI habilitados: active o inactive'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $statusFilter = $this->opt($parsed, 'status');
        if ($statusFilter !== null && !in_array($statusFilter, ['active', 'inactive'], true)) {
            echo "Valor inválido para --status. Usa active o inactive.\n";
            return;
        }

        $packagesPath = rtrim(PACKAGES_PATH, '/\\');
        if (!is_dir($packagesPath)) {
            echo "No hay directorio de packages en {$packagesPath}.\n";
            return;
        }

        $commandsConfig = require ROOT_PATH . 'config' . DIRECTORY_SEPARATOR . 'commands.php';
        $enabledPackages = $commandsConfig['packages'] ?? [];
        $packages = [];
        foreach (new \DirectoryIterator($packagesPath) as $authorDirectory) {
            if ($authorDirectory->isDot() || !$authorDirectory->isDir()) {
                continue;
            }

            foreach (new \DirectoryIterator($authorDirectory->getPathname()) as $packageDirectory) {
                if ($packageDirectory->isDot() || !$packageDirectory->isDir()) {
                    continue;
                }

                $packageId = $authorDirectory->getFilename() . '/' . $packageDirectory->getFilename();
                $isCliActive = in_array($packageId, $enabledPackages, true);
                if ($statusFilter === 'active' && !$isCliActive) {
                    continue;
                }
                if ($statusFilter === 'inactive' && $isCliActive) {
                    continue;
                }

                $packages[] = [
                    'id' => $packageId,
                    'composer' => $this->isComposerPackageAvailable($packageId) ? 'yes' : 'no',
                    'cli' => $isCliActive ? 'active' : 'inactive',
                ];
            }
        }

        usort($packages, static fn(array $left, array $right): int => strnatcasecmp($left['id'], $right['id']));
        if ($packages === []) {
            $filterLabel = $statusFilter === null ? '' : " con estado CLI '{$statusFilter}'";
            echo "No hay packages locales{$filterLabel} en {$packagesPath}.\n";
            return;
        }

        printf("%-45s %-18s %s\n", 'PACKAGE', 'COMPOSER AUTOLOAD', 'CLI COMMANDS');
        foreach ($packages as $package) {
            printf("%-45s %-18s %s\n", $package['id'], $package['composer'], $package['cli']);
        }
    }

    private function isComposerPackageAvailable(string $packageId): bool
    {
        if (class_exists('Composer\\InstalledVersions')
            && method_exists('Composer\\InstalledVersions', 'isInstalled')
            && \Composer\InstalledVersions::isInstalled($packageId)) {
            return true;
        }

        if (!class_exists('Composer\\Autoload\\ClassLoader')
            || !method_exists('Composer\\Autoload\\ClassLoader', 'getRegisteredLoaders')) {
            return false;
        }

        $packagePath = realpath(PACKAGES_PATH . str_replace('/', DIRECTORY_SEPARATOR, $packageId));
        if ($packagePath === false) {
            return false;
        }

        $packagePath = rtrim($packagePath, '/\\') . DIRECTORY_SEPARATOR;
        foreach (\Composer\Autoload\ClassLoader::getRegisteredLoaders() as $loader) {
            foreach ($loader->getClassMap() as $classPath) {
                $resolvedPath = realpath($classPath);
                if ($resolvedPath !== false && str_starts_with($resolvedPath, $packagePath)) {
                    return true;
                }
            }

            foreach ($loader->getPrefixesPsr4() as $paths) {
                foreach ($paths as $path) {
                    $resolvedPath = realpath($path);
                    if ($resolvedPath !== false && str_starts_with(rtrim($resolvedPath, '/\\') . DIRECTORY_SEPARATOR, $packagePath)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
