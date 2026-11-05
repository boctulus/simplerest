<?php

declare(strict_types=1);
namespace Boctulus\Simplerest\Core\Libs;

/**
 * Read-only discovery of migration files in the application, module and package roots.
 */
final class MigrationCatalog
{
    /**
     * Return migration files from the application, module and Composer-style package roots.
     *
     * @return array<int, array{source: string, relative_path: string, filename: string, absolute_path: string, directory: string}>
     */
    public static function discover(): array
    {
        $projectRoot = realpath(ROOT_PATH);
        if ($projectRoot === false) {
            return [];
        }

        $roots = [];
        self::addRoot($roots, MIGRATIONS_PATH, 'app');

        $modulePatterns = [
            ROOT_PATH . 'modules' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations',
            MODULES_PATH . '*' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations',
        ];
        foreach ($modulePatterns as $pattern) {
            foreach (self::matchingDirectories($pattern) as $path) {
                $modulePath = dirname(dirname($path));
                $roots[] = ['path' => $path, 'source' => 'module:' . basename($modulePath)];
            }
        }

        $packagePatterns = [
            ROOT_PATH . 'packages' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations',
            PACKAGES_PATH . '*' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations',
        ];
        foreach ($packagePatterns as $pattern) {
            foreach (self::matchingDirectories($pattern) as $path) {
                $packagePath = dirname(dirname($path));
                $vendor = basename(dirname($packagePath));
                $package = basename($packagePath);
                $roots[] = ['path' => $path, 'source' => 'package:' . $vendor . '/' . $package];
            }
        }

        usort($roots, static function (array $left, array $right): int {
            return strcmp($left['source'], $right['source']);
        });

        $migrations = [];
        $seenFiles = [];
        foreach ($roots as $root) {
            $migrationRoot = realpath($root['path']);
            if ($migrationRoot === false || !self::isWithin($migrationRoot, $projectRoot)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($migrationRoot, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $fileInfo) {
                if (!$fileInfo->isFile() || $fileInfo->isLink() || strtolower($fileInfo->getExtension()) !== 'php') {
                    continue;
                }

                $absolutePath = realpath($fileInfo->getPathname());
                if ($absolutePath === false || !self::isWithin($absolutePath, $migrationRoot) || !self::isWithin($absolutePath, $projectRoot)) {
                    continue;
                }

                $relativePath = self::relativePath($absolutePath, $projectRoot);
                if (self::containsHiddenSegment($relativePath)) {
                    continue;
                }

                $seenKey = self::pathKey($absolutePath);
                if (isset($seenFiles[$seenKey])) {
                    continue;
                }
                $seenFiles[$seenKey] = true;

                $migrations[] = [
                    'source' => $root['source'],
                    'relative_path' => $relativePath,
                    'filename' => basename($absolutePath),
                    'absolute_path' => $absolutePath,
                    'directory' => dirname($absolutePath),
                ];
            }
        }

        usort($migrations, static function (array $left, array $right): int {
            return strcmp($left['relative_path'], $right['relative_path']);
        });

        return $migrations;
    }

    /**
     * Map a migrations-table row to the project-relative migration identity used by discovery.
     * Legacy null paths refer to the root application migration directory.
     */
    public static function identityFromRegistryRow(array $row): ?string
    {
        $filename = (string) ($row['filename'] ?? '');
        if ($filename === '' || basename(str_replace('\\', '/', $filename)) !== $filename) {
            return null;
        }

        $storedPath = trim((string) ($row['path'] ?? ''));
        if ($storedPath === '') {
            return 'database/migrations/' . $filename;
        }

        $normalizedPath = str_replace('\\', '/', $storedPath);
        if (self::isAbsolute($normalizedPath)) {
            $recordFile = rtrim($normalizedPath, '/') . '/' . $filename;
            $realRecordFile = realpath($recordFile);
            $projectRoot = realpath(ROOT_PATH);
            if ($realRecordFile !== false && $projectRoot !== false && self::isWithin($realRecordFile, $projectRoot)) {
                return self::relativePath($realRecordFile, $projectRoot);
            }

            return self::identityFromAbsoluteRegistryPath($normalizedPath, $filename);
        }

        $normalizedPath = trim(preg_replace('#/+#', '/', $normalizedPath), '/');
        if (preg_match('#^(database/migrations|modules/|packages/|app/Modules/|app/Packages/)#i', $normalizedPath)) {
            return $normalizedPath . '/' . $filename;
        }

        return 'database/migrations/' . ($normalizedPath !== '' ? $normalizedPath . '/' : '') . $filename;
    }

    public static function identityKey(string $relativePath): string
    {
        $relativePath = str_replace('\\', '/', $relativePath);
        return DIRECTORY_SEPARATOR === '\\' ? strtolower($relativePath) : $relativePath;
    }

    /** @return array<int, string> */
    private static function matchingDirectories(string $pattern): array
    {
        $pattern = str_replace('\\', '/', $pattern);
        $matches = glob($pattern, GLOB_ONLYDIR) ?: [];
        $matches = array_values(array_filter($matches, 'is_dir'));
        sort($matches, SORT_STRING);
        return $matches;
    }

    /** @param array<int, array{path: string, source: string}> $roots */
    private static function addRoot(array &$roots, string $path, string $source): void
    {
        if (is_dir($path)) {
            $roots[] = ['path' => $path, 'source' => $source];
        }
    }

    private static function relativePath(string $path, string $root): string
    {
        $path = str_replace('\\', '/', $path);
        $root = rtrim(str_replace('\\', '/', $root), '/');
        return ltrim(substr($path, strlen($root)), '/');
    }

    private static function isWithin(string $path, string $root): bool
    {
        $path = rtrim(str_replace('\\', '/', $path), '/');
        $root = rtrim(str_replace('\\', '/', $root), '/');
        $prefix = $root . '/';

        if (DIRECTORY_SEPARATOR === '\\') {
            $path = strtolower($path);
            $root = strtolower($root);
            $prefix = strtolower($prefix);
        }

        return $path === $root || str_starts_with($path, $prefix);
    }

    private static function isAbsolute(string $path): bool
    {
        return str_starts_with($path, '/')
            || str_starts_with($path, '\\')
            || (strlen($path) >= 3 && ctype_alpha($path[0]) && $path[1] === ':' && ($path[2] === '/' || $path[2] === '\\'));
    }

    private static function identityFromAbsoluteRegistryPath(string $path, string $filename): ?string
    {
        $path = trim(str_replace('\\', '/', $path), '/');
        $patterns = [
            '#(?:^|/)(packages/[^/]+/[^/]+/database/migrations(?:/.*)?)$#i',
            '#(?:^|/)(app/Packages/[^/]+/[^/]+/database/migrations(?:/.*)?)$#i',
            '#(?:^|/)(modules/[^/]+/database/migrations(?:/.*)?)$#i',
            '#(?:^|/)(app/Modules/[^/]+/database/migrations(?:/.*)?)$#i',
            '#(?:^|/)(database/migrations(?:/.*)?)$#i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $path, $matches)) {
                return rtrim($matches[1], '/') . '/' . $filename;
            }
        }

        return null;
    }

    private static function pathKey(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        return DIRECTORY_SEPARATOR === '\\' ? strtolower($path) : $path;
    }

    private static function containsHiddenSegment(string $relativePath): bool
    {
        foreach (explode('/', $relativePath) as $segment) {
            if ($segment !== '' && $segment[0] === '.') {
                return true;
            }
        }

        return false;
    }
}
