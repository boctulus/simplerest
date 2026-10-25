<?php

declare(strict_types=1);

/**
 * Dependency-free Composer core pack builder (the whole src/framework tree).
 *
 * This is the implementation of "php com pack build". It runs BEFORE the
 * application's old CLI/bootstrap and never loads optional framework code.
 * It reads only committed blobs (not dirty working-tree files).
 */
final class SimpleRestCorePackBuilder
{
    public static function main(array $argv): int
    {
        if (in_array('--help', $argv, true) || in_array('-h', $argv, true)) {
            self::printHelp();
            return 0;
        }

        try {
            self::build($argv);
            return 0;
        } catch (Throwable $error) {
            fwrite(STDERR, "[pack] " . $error->getMessage() . PHP_EOL);
            return 1;
        }
    }

    private static function printHelp(): void
    {
        echo <<<'HELP'
Usage:
  php com pack build [--profile=core] [--source=<path>] [--dest=<path>] [--dry-run]
  php com pack dry-run [--profile=core] [--source=<path>] [--dest=<path>]
  php com pack build --source-commit=<sha> [--dest=<path>]

PACK creates the curated Composer microcore locally. It never verifies a release,
publishes a tag or package, or updates a consuming application.

Options:
  --profile=core              Build the sole supported Composer core profile.
  --source=<path>             Canonical SimpleRest Git repository.
  --dest=<path>               Curated distribution destination.
  --source-commit=<sha>       Deliberately build an immutable historical commit.
  --dry-run                   Run full preflight and show operations without writes.
  --help, -h                  Show this help without starting a build.

HELP;
    }

    private static function build(array $argv): void
    {
        $options = [];
        foreach ($argv as $arg) {
            if ($arg === '--dry-run') {
                $options['dry-run'] = true;
                continue;
            }
            if (!preg_match('/^--(source|dest|source-commit|profile)=(.+)$/', $arg, $match)) {
                throw new InvalidArgumentException("Unsupported pack option: $arg");
            }
            $options[$match[1]] = $match[2];
        }

        $profile = $options['profile'] ?? 'core';
        if ($profile !== 'core') {
            throw new RuntimeException('Only --profile=core is available for the Composer release pipeline. The old starter packer is not safe after the microcore split.');
        }

        $source = realpath($options['source'] ?? dirname(__DIR__, 2));
        if ($source === false || !is_file($source . DIRECTORY_SEPARATOR . 'composer.json')) {
            throw new RuntimeException('Canonical SimpleRest source directory not found.');
        }
        $repoRoot = trim(self::git($source, ['rev-parse', '--show-toplevel']));
        if (realpath($repoRoot) !== $source) {
            throw new RuntimeException('Source must be the root of the canonical SimpleRest Git repository.');
        }

        $head = trim(self::git($source, ['rev-parse', 'HEAD^{commit}']));
        $explicitCommit = array_key_exists('source-commit', $options);
        $commit = trim(self::git($source, ['rev-parse', ($options['source-commit'] ?? 'HEAD') . '^{commit}']));
        if (!preg_match('/^[a-f0-9]{40}$/', $commit)) {
            throw new RuntimeException('Could not resolve an immutable source commit.');
        }
        if (!$explicitCommit) {
            self::assertRelevantSourceClean($source);
        }
        $sourceMode = !$explicitCommit
            ? 'current-head'
            : ($commit === $head ? 'explicit-commit' : 'historical-commit');

        $destination = self::validatedDestinationPath(
            $options['dest'] ?? dirname($source) . DIRECTORY_SEPARATOR . 'simplerest-pack',
            $source
        );

        // The core is the whole committed src/framework tree of the SAME immutable
        // source commit, plus the root package files. Libraries too specific for
        // the core live in packages/boctulus, never in a separate core namespace.
        $required = ['composer.json', 'LICENSE', 'README.md'];
        $listed = trim(self::git($source, ['ls-tree', '-r', '--name-only', $commit, '--', 'src/framework']));
        $frameworkPaths = $listed === '' ? [] : preg_split('/\r?\n/', $listed);
        if ($frameworkPaths === []) {
            throw new RuntimeException('The source commit has no src/framework tree.');
        }
        foreach ($frameworkPaths as $path) {
            if (!preg_match('~^src/framework/[A-Za-z0-9_./-]+$~D', $path) || str_contains($path, '..')) {
                throw new RuntimeException("Unexpected path inside the core tree: $path");
            }
        }
        $paths = array_merge($required, $frameworkPaths);

        $manifest = json_decode(self::git($source, ['show', $commit . ':composer.json']), true, 512, JSON_THROW_ON_ERROR);
        if (($manifest['name'] ?? null) !== 'boctulus/simplerest'
            // php plus the approved doctrine/inflector exception (SubResourceHandler)
            || array_keys($manifest['require'] ?? []) !== ['php', 'doctrine/inflector']
            || isset($manifest['autoload']['files'])
            || isset($manifest['suggest'])
            || isset($manifest['require-dev'])) {
            throw new RuntimeException('Source manifest is not a self-contained microcore-only Composer manifest.');
        }

        // Root-only Composer hooks belong to the canonical development checkout.
        // The curated library never contains or references development scripts.
        unset($manifest['scripts']);
        $curatedManifest = json_encode(
            $manifest,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        ) . PHP_EOL;

        $contents = self::readCommittedFiles($source, $commit, $paths);
        $contents['composer.json'] = $curatedManifest;
        $hashes = [];
        foreach ($paths as $path) {
            $hashes[$path] = hash('sha256', $contents[$path]);
        }
        ksort($hashes);

        $existingPaths = [];
        if (file_exists($destination) || is_link($destination)) {
            $existingPaths = self::preflightExistingDestination($destination);
        }

        printf(
            "[pack] profile=core source_mode=%s commit=%s head=%s files=%d destination=%s\n",
            $sourceMode,
            $commit,
            $head,
            count($paths),
            $destination
        );
        if ($sourceMode === 'historical-commit') {
            echo "[pack] Historical build explicitly requested; this is not the current development HEAD." . PHP_EOL;
        }
        self::reportOperations($destination, $existingPaths, $paths, $hashes);
        if (isset($options['dry-run'])) {
            echo "[pack] Dry-run complete; no files were modified." . PHP_EOL;
            return;
        }

        $stage = dirname($destination) . DIRECTORY_SEPARATOR . '.simplerest-core-stage-' . bin2hex(random_bytes(8));
        if (!mkdir($stage, 0700)) {
            throw new RuntimeException('Could not create the isolated package staging directory.');
        }
        try {
            foreach ($paths as $path) {
                $target = $stage . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
                if (!is_dir(dirname($target)) && !mkdir(dirname($target), 0775, true)) {
                    throw new RuntimeException("Cannot stage $path");
                }
                if (file_put_contents($target, $contents[$path]) !== strlen($contents[$path])) {
                    throw new RuntimeException("Incomplete staged file: $path");
                }
                if (str_ends_with($path, '.php')) {
                    self::lintPhp($contents[$path], $path);
                }
            }
            if (!file_exists($destination)) {
                if (!rename($stage, $destination)) {
                    throw new RuntimeException('Could not atomically install the new curated distribution.');
                }
                $stage = '';
            } else {
                self::replaceManagedDestination($destination, $stage, $existingPaths, $paths);
            }
        } finally {
            if ($stage !== '' && is_dir($stage)) {
                self::removeOwnedTemporaryDirectory($stage, dirname($destination), '.simplerest-core-stage-');
            }
        }
        echo "[pack] Curated Composer microcore generated successfully. No release or Packagist publication occurred." . PHP_EOL;
    }

    private static function assertRelevantSourceClean(string $source): void
    {
        $status = self::git($source, [
            'status',
            '--porcelain=v1',
            '-z',
            '--untracked-files=all',
            '--ignored=matching',
            '--',
            'composer.json',
            'LICENSE',
            'README.md',
            'src/framework',
        ]);
        if ($status === '') {
            return;
        }

        $entries = array_values(array_filter(explode("\0", $status), static fn (string $entry): bool => $entry !== ''));
        $summary = implode(', ', array_slice($entries, 0, 10));
        if (count($entries) > 10) {
            $summary .= sprintf(', ... (%d entries)', count($entries));
        }
        throw new RuntimeException(
            'Relevant source changes would be omitted from the default HEAD build: ' . $summary
            . '. Commit them, or deliberately select an immutable commit with --source-commit=<sha>.'
        );
    }

    private static function validatedDestinationPath(string $path, string $source): string
    {
        $absolute = self::absolutePath($path);
        $parentInput = dirname($absolute);
        $parent = realpath($parentInput);
        if ($parent === false || !is_dir($parent)) {
            throw new RuntimeException('Destination parent directory must already exist.');
        }
        if (self::normalizedPath($parent) !== self::normalizedPath($parentInput)) {
            throw new RuntimeException('Destination parent cannot be reached through a symlink or junction.');
        }

        $destination = $parent . DIRECTORY_SEPARATOR . basename($absolute);
        if (self::pathsOverlap($source, $destination)) {
            throw new RuntimeException('Source and destination must be distinct, non-overlapping directories.');
        }
        if (file_exists($destination) || is_link($destination)) {
            if (!is_dir($destination) || is_link($destination)) {
                throw new RuntimeException('Destination must be a real directory, not a file or symlink.');
            }
            $resolved = realpath($destination);
            if ($resolved === false || self::normalizedPath($resolved) !== self::normalizedPath($destination)) {
                throw new RuntimeException('Destination cannot be a symlink or junction.');
            }
        }

        return $destination;
    }

    private static function preflightExistingDestination(string $directory): array
    {
        if (!file_exists($directory . DIRECTORY_SEPARATOR . '.git')) {
            throw new RuntimeException(
                'An existing destination is recognized only as a clean Git-managed core distribution. '
                . 'PACK will not infer ownership or migrate an unversioned directory.'
            );
        }
        $repoRoot = realpath(trim(self::git($directory, ['rev-parse', '--show-toplevel'])));
        if ($repoRoot === false || self::normalizedPath($repoRoot) !== self::normalizedPath($directory)) {
            throw new RuntimeException('Destination must be the root of its own Git worktree.');
        }
        $status = trim(self::git($directory, [
            'status',
            '--porcelain=v1',
            '--untracked-files=all',
            '--ignored=matching',
        ]));
        if ($status !== '') {
            throw new RuntimeException(
                'Curated destination Git worktree has staged, unstaged, untracked, or ignored files. '
                . 'Preserve other agents\' work; refusing to overwrite.'
            );
        }

        $trackedOutput = self::git($directory, ['ls-files', '-z']);
        $tracked = array_values(array_filter(explode("\0", $trackedOutput), static fn (string $path): bool => $path !== ''));
        sort($tracked);
        if ($tracked === []) {
            throw new RuntimeException('Destination Git worktree has no tracked package files.');
        }
        foreach ($tracked as $path) {
            if (!self::isCorePackagePath($path)) {
                throw new RuntimeException(
                    "Destination contains tracked legacy or unrelated content: $path. "
                    . 'An explicit, separately reviewed migration is required.'
                );
            }
        }
        $actual = self::listSafeDestinationFiles($directory);
        if ($actual !== $tracked) {
            throw new RuntimeException(
                'Destination filesystem does not exactly match its tracked core package files; refusing to modify it.'
            );
        }
        $destinationManifest = json_decode(
            (string) file_get_contents($directory . DIRECTORY_SEPARATOR . 'composer.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        if (($destinationManifest['name'] ?? null) !== 'boctulus/simplerest') {
            throw new RuntimeException('Destination Git worktree is not the boctulus/simplerest core package.');
        }

        return $tracked;
    }

    private static function isCorePackagePath(string $path): bool
    {
        return in_array($path, ['composer.json', 'LICENSE', 'README.md'], true)
            || (bool) preg_match('~^src/framework/[A-Za-z0-9_./-]+$~D', $path);
    }

    private static function listSafeDestinationFiles(string $directory): array
    {
        $files = [];
        $queue = [['absolute' => $directory, 'relative' => '']];
        while ($queue !== []) {
            $current = array_pop($queue);
            $entries = scandir($current['absolute']);
            if ($entries === false) {
                throw new RuntimeException('Cannot inspect the destination tree.');
            }
            foreach ($entries as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                $relative = ltrim($current['relative'] . '/' . $entry, '/');
                if ($relative === '.git') {
                    continue;
                }
                $absolute = $current['absolute'] . DIRECTORY_SEPARATOR . $entry;
                if (is_link($absolute)) {
                    throw new RuntimeException("Symlinks are not allowed inside the curated destination: $relative");
                }
                $resolved = realpath($absolute);
                if ($resolved === false || self::normalizedPath($resolved) !== self::normalizedPath($absolute)) {
                    throw new RuntimeException("Junctions or unresolved filesystem entries are not allowed: $relative");
                }
                if (is_dir($absolute)) {
                    $queue[] = ['absolute' => $absolute, 'relative' => $relative];
                } elseif (is_file($absolute)) {
                    $files[] = str_replace('\\', '/', $relative);
                } else {
                    throw new RuntimeException("Unsupported filesystem entry in destination: $relative");
                }
            }
        }
        sort($files);
        return $files;
    }

    private static function reportOperations(
        string $destination,
        array $existingPaths,
        array $managedPaths,
        array $hashes
    ): void {
        $existing = array_fill_keys($existingPaths, true);
        $next = array_fill_keys($managedPaths, true);
        foreach ($existingPaths as $path) {
            if (!isset($next[$path])) {
                echo "[pack] REMOVE managed $path" . PHP_EOL;
            }
        }
        foreach ($managedPaths as $path) {
            $target = $destination . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
            $operation = !isset($existing[$path])
                ? 'CREATE'
                : (is_file($target) && hash_equals($hashes[$path], (string) hash_file('sha256', $target)) ? 'KEEP' : 'UPDATE');
            echo "[pack] $operation $path" . PHP_EOL;
        }
    }

    private static function replaceManagedDestination(
        string $destination,
        string $stage,
        array $existingPaths,
        array $managedPaths
    ): void {
        $parent = dirname($destination);
        $backup = $parent . DIRECTORY_SEPARATOR . '.simplerest-core-backup-' . bin2hex(random_bytes(8));
        if (!mkdir($backup, 0700)) {
            throw new RuntimeException('Could not create the isolated PACK rollback directory.');
        }

        $installed = [];
        try {
            foreach ($existingPaths as $path) {
                self::moveManagedFile($destination, $backup, $path);
            }
            foreach ($managedPaths as $path) {
                self::moveManagedFile($stage, $destination, $path);
                $installed[] = $path;
            }
        } catch (Throwable $error) {
            $rollbackErrors = [];
            foreach (array_reverse($installed) as $path) {
                $installedPath = $destination . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
                if (is_file($installedPath) && !unlink($installedPath)) {
                    $rollbackErrors[] = "cannot remove installed $path";
                }
            }
            foreach ($existingPaths as $path) {
                $backupPath = $backup . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
                if (is_file($backupPath)) {
                    try {
                        self::moveManagedFile($backup, $destination, $path);
                    } catch (Throwable $rollbackError) {
                        $rollbackErrors[] = $rollbackError->getMessage();
                    }
                }
            }
            if ($rollbackErrors !== []) {
                throw new RuntimeException(
                    'PACK replacement failed and rollback needs manual recovery from ' . $backup . ': '
                    . implode('; ', $rollbackErrors),
                    0,
                    $error
                );
            }
            throw new RuntimeException('PACK replacement failed; the original destination was restored: ' . $error->getMessage(), 0, $error);
        } finally {
            if (is_dir($backup) && self::listSafeDestinationFiles($backup) === []) {
                self::removeOwnedTemporaryDirectory($backup, $parent, '.simplerest-core-backup-');
            }
        }

        self::removeOwnedTemporaryDirectory($backup, $parent, '.simplerest-core-backup-');
    }

    private static function moveManagedFile(string $fromRoot, string $toRoot, string $path): void
    {
        $relative = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $from = $fromRoot . DIRECTORY_SEPARATOR . $relative;
        $to = $toRoot . DIRECTORY_SEPARATOR . $relative;
        if (!is_file($from)) {
            throw new RuntimeException("Managed file is missing during replacement: $path");
        }
        if (!is_dir(dirname($to)) && !mkdir(dirname($to), 0775, true)) {
            throw new RuntimeException("Cannot prepare managed destination directory for $path");
        }
        if (!rename($from, $to)) {
            throw new RuntimeException("Cannot move managed file during replacement: $path");
        }
    }

    private static function git(string $dir, array $args): string
    {
        $command = array_merge(['git', '-C', $dir], $args);
        $process = proc_open($command, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        if (!is_resource($process)) {
            throw new RuntimeException('Git could not be started.');
        }
        $output = stream_get_contents($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        if (proc_close($process) !== 0) {
            throw new RuntimeException('Git failed: ' . trim($error));
        }
        return $output;
    }

    private static function readCommittedFiles(string $source, string $commit, array $paths): array
    {
        $process = proc_open(
            ['git', '-C', $source, 'cat-file', '--batch'],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );
        if (!is_resource($process)) {
            throw new RuntimeException('Git could not start committed file extraction.');
        }
        $contents = [];
        foreach ($paths as $path) {
            if (fwrite($pipes[0], $commit . ':' . $path . "\n") === false || !fflush($pipes[0])) {
                throw new RuntimeException("Cannot request committed package file: $path");
            }
            $header = fgets($pipes[1]);
            if ($header === false || !preg_match('/^[a-f0-9]+ blob ([0-9]+)\n$/D', $header, $match)) {
                throw new RuntimeException("Cannot read committed package file: $path");
            }
            $remaining = (int) $match[1];
            $content = '';
            while ($remaining > 0) {
                $chunk = fread($pipes[1], $remaining);
                if ($chunk === false || $chunk === '') {
                    throw new RuntimeException("Incomplete committed package file: $path");
                }
                $content .= $chunk;
                $remaining -= strlen($chunk);
            }
            if (fread($pipes[1], 1) !== "\n") {
                throw new RuntimeException("Invalid Git batch delimiter after package file: $path");
            }
            $contents[$path] = $content;
        }
        fclose($pipes[0]);
        fclose($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        if (proc_close($process) !== 0) {
            throw new RuntimeException('Git committed file extraction failed: ' . trim($error));
        }
        return $contents;
    }

    private static function lintPhp(string $content, string $path): void
    {
        try {
            token_get_all($content, TOKEN_PARSE);
        } catch (ParseError $error) {
            throw new RuntimeException("PHP syntax validation failed for $path: " . $error->getMessage(), 0, $error);
        }
    }

    private static function absolutePath(string $path): string
    {
        if (preg_match('~^(?:[A-Za-z]:[\\\\/]|/|\\\\\\\\)~', $path)) {
            return rtrim($path, '\\/');
        }
        return rtrim(getcwd() . DIRECTORY_SEPARATOR . $path, '\\/');
    }

    private static function normalizedPath(string $path): string
    {
        $normalized = rtrim(str_replace('\\', '/', $path), '/');
        return DIRECTORY_SEPARATOR === '\\' ? strtolower($normalized) : $normalized;
    }

    private static function pathsOverlap(string $first, string $second): bool
    {
        $first = self::normalizedPath($first);
        $second = self::normalizedPath($second);
        return $first === $second
            || str_starts_with($first . '/', $second . '/')
            || str_starts_with($second . '/', $first . '/');
    }

    private static function removeOwnedTemporaryDirectory(string $path, string $parent, string $prefix): void
    {
        $resolvedPath = realpath($path);
        $resolvedParent = realpath($parent);
        if ($resolvedPath === false || $resolvedParent === false
            || self::normalizedPath(dirname($resolvedPath)) !== self::normalizedPath($resolvedParent)
            || !str_starts_with(basename($resolvedPath), $prefix)) {
            throw new RuntimeException("Refusing to remove an unverified PACK temporary directory: $path");
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($resolvedPath, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $entry) {
            if ($entry->isLink()) {
                throw new RuntimeException('Refusing to follow a link while removing PACK temporary files.');
            }
            $entry->isDir() ? rmdir($entry->getPathname()) : unlink($entry->getPathname());
        }
        rmdir($resolvedPath);
    }
}
