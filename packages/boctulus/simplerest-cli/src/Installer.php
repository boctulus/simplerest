<?php

declare(strict_types=1);

namespace Boctulus\Simplerest\Cli;

/**
 * Installs the minimal application bootstrap (com, app.php, config/, ...) into
 * a project that requires boctulus/simplerest with Composer.
 *
 * Existing files are never overwritten: they are reported as SKIP.
 */
final class Installer
{
    /** Directories every SimpleRest application expects. */
    private const DIRECTORIES = [
        'app/Commands',
        'app/Controllers',
        'app/Helpers',
        'app/Middlewares',
        'app/Models',
        'app/Modules',
        'app/Schemas',
        'app/Views',
        'cache',
        'database/migrations',
        'logs',
        'storage/security',
    ];

    private string $stubsPath;

    public function __construct(?string $stubsPath = null)
    {
        $this->stubsPath = $stubsPath ?? dirname(__DIR__) . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'project';
    }

    /**
     * @return list<array{action: string, path: string}> CREATE, MKDIR or SKIP per relative path
     */
    public function init(string $projectRoot, bool $dryRun = false): array
    {
        $root = rtrim($projectRoot, '/\\');
        if (!is_file($root . DIRECTORY_SEPARATOR . 'composer.json')) {
            throw new \InvalidArgumentException("Not a Composer project (composer.json not found): {$root}");
        }
        if (!is_dir($this->stubsPath)) {
            throw new \RuntimeException("Stub directory not found: {$this->stubsPath}");
        }

        $report = [];

        foreach ($this->stubFiles() as $relative) {
            $report[] = $this->copy($this->stubsPath . DIRECTORY_SEPARATOR . $relative, $root, $relative, $dryRun);
        }

        $envExample = $root . DIRECTORY_SEPARATOR . '.env.example';
        if (is_file($envExample) || $dryRun) {
            $report[] = $this->copy($envExample, $root, '.env', $dryRun);
        }

        foreach (self::DIRECTORIES as $directory) {
            $target = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $directory);
            if (is_dir($target)) {
                continue;
            }
            if (!$dryRun && !mkdir($target, 0775, true) && !is_dir($target)) {
                throw new \RuntimeException("Could not create directory: {$target}");
            }
            $report[] = ['action' => 'MKDIR', 'path' => $directory];
        }

        return $report;
    }

    /** @return list<string> stub paths relative to the stub root, using '/' */
    private function stubFiles(): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->stubsPath, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relative = substr($file->getPathname(), strlen($this->stubsPath) + 1);
                $files[] = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
            }
        }

        sort($files);
        return $files;
    }

    /** @return array{action: string, path: string} */
    private function copy(string $source, string $root, string $relative, bool $dryRun): array
    {
        $target = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);

        if (file_exists($target)) {
            return ['action' => 'SKIP', 'path' => $relative];
        }

        if (!$dryRun) {
            $directory = dirname($target);
            if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
                throw new \RuntimeException("Could not create directory: {$directory}");
            }
            if (!copy($source, $target)) {
                throw new \RuntimeException("Could not write file: {$target}");
            }
            if ($relative === 'com') {
                @chmod($target, 0755);
            }
        }

        return ['action' => 'CREATE', 'path' => $relative];
    }
}
