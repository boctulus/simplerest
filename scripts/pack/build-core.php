<?php

declare(strict_types=1);

/**
 * Curated, dependency-free Composer microcore pack builder.
 *
 * This is the implementation of "php com pack build". It runs BEFORE the
 * application's old CLI/bootstrap and never loads optional framework code.
 * It reads only committed blobs (not dirty working-tree files).
 */
final class SimpleRestCorePackBuilder
{
    public static function main(array $argv): int
    {
        try {
            self::build($argv);
            return 0;
        } catch (Throwable $error) {
            fwrite(STDERR, "[pack] " . $error->getMessage() . PHP_EOL);
            return 1;
        }
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

        $commit = trim(self::git($source, ['rev-parse', ($options['source-commit'] ?? 'HEAD') . '^{commit}']));
        if (!preg_match('/^[a-f0-9]{40}$/', $commit)) {
            throw new RuntimeException('Could not resolve an immutable source commit.');
        }

        $destination = $options['dest'] ?? dirname($source) . DIRECTORY_SEPARATOR . 'simplerest-pack';
        $destination = self::absolutePath($destination);
        $sourceNormalized = rtrim(str_replace('\\', '/', $source), '/');
        $destNormalized = rtrim(str_replace('\\', '/', $destination), '/');
        if (strtolower($destNormalized) === strtolower($sourceNormalized)
            || str_starts_with(strtolower($destNormalized) . '/', strtolower($sourceNormalized) . '/')) {
            throw new RuntimeException('The pack destination must be outside the source repository.');
        }

        $listed = trim(self::git($source, [
            'ls-tree', '-r', '--name-only', $commit, '--',
            'composer.json', 'LICENSE', 'README.md', 'src/framework'
        ]));
        $paths = $listed === '' ? [] : preg_split('/\r?\n/', $listed);
        $required = ['composer.json', 'LICENSE', 'README.md'];
        foreach ($required as $path) {
            if (!in_array($path, $paths, true)) {
                throw new RuntimeException("Required package file missing from commit: $path");
            }
        }
        $phpFiles = 0;
        foreach ($paths as $path) {
            if (!in_array($path, $required, true)
                && !preg_match('~^src/framework/[a-zA-Z0-9_/-]+\.php$~D', $path)) {
                throw new RuntimeException("Unexpected source inside curated core: $path");
            }
            if (str_starts_with($path, 'src/framework/')) {
                $phpFiles++;
            }
        }
        if ($phpFiles === 0) {
            throw new RuntimeException('Refusing to build an empty HTTP microcore.');
        }

        $manifest = json_decode(self::git($source, ['show', $commit . ':composer.json']), true, 512, JSON_THROW_ON_ERROR);
        if (($manifest['name'] ?? null) !== 'boctulus/simplerest'
            || array_keys($manifest['require'] ?? []) !== ['php']
            || isset($manifest['autoload']['files'])
            || isset($manifest['suggest'])
            || isset($manifest['require-dev'])) {
            throw new RuntimeException('Source manifest is not a self-contained microcore-only Composer manifest.');
        }

        printf("[pack] profile=core commit=%s files=%d destination=%s\n", $commit, count($paths), $destination);
        if (isset($options['dry-run'])) {
            foreach ($paths as $path) {
                echo "[pack] " . $path . PHP_EOL;
            }
            return;
        }

        if (!is_dir(dirname($destination))) {
            throw new RuntimeException('Destination parent directory must already exist.');
        }
        $allowed = array_fill_keys($paths, true);
        if (file_exists($destination)) {
            if (!is_dir($destination) || is_link($destination)) {
                throw new RuntimeException('Destination must be a real directory, not a file or symlink.');
            }
            if (file_exists($destination . DIRECTORY_SEPARATOR . '.git')) {
                $status = trim(self::git($destination, ['status', '--porcelain', '--untracked-files=all']));
                if ($status !== '') {
                    throw new RuntimeException('Curated destination Git worktree is dirty. Preserve other agents\' work; refusing to overwrite.');
                }
            }
            self::assertOnlyManagedFiles($destination, $allowed);
        }

        // Stage every byte before updating the destination. Never clean an
        // existing directory recursively and never touch its Git metadata.
        $stage = dirname($destination) . DIRECTORY_SEPARATOR . '.simplerest-core-stage-' . bin2hex(random_bytes(8));
        if (!mkdir($stage, 0700)) {
            throw new RuntimeException('Could not create the isolated package staging directory.');
        }
        try {
            foreach ($paths as $path) {
                $content = self::git($source, ['show', $commit . ':' . $path]);
                $target = $stage . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
                if (!is_dir(dirname($target)) && !mkdir(dirname($target), 0775, true)) {
                    throw new RuntimeException("Cannot stage $path");
                }
                if (file_put_contents($target, $content) !== strlen($content)) {
                    throw new RuntimeException("Incomplete staged file: $path");
                }
                if (str_ends_with($path, '.php')) {
                    self::lintPhp($target);
                }
            }

            if (!file_exists($destination)) {
                if (!rename($stage, $destination)) {
                    throw new RuntimeException('Could not atomically install the new curated distribution.');
                }
                $stage = '';
            } else {
                foreach ($paths as $path) {
                    $target = $destination . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
                    $staged = $stage . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
                    if (!is_dir(dirname($target)) && !mkdir(dirname($target), 0775, true)) {
                        throw new RuntimeException("Cannot prepare destination for $path");
                    }
                    if (is_file($target) && hash_file('sha256', $target) === hash_file('sha256', $staged)) {
                        continue;
                    }
                    if (!copy($staged, $target)) {
                        throw new RuntimeException("Cannot update curated file $path");
                    }
                }
            }
        } finally {
            if ($stage !== '' && is_dir($stage)) {
                self::removeOwnedStage($stage);
            }
        }
        echo "[pack] Curated Composer microcore generated successfully. No release or Packagist publication occurred." . PHP_EOL;
    }

    private static function assertOnlyManagedFiles(string $directory, array $allowed): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $entry) {
            $relative = str_replace('\\', '/', substr($entry->getPathname(), strlen($directory) + 1));
            if ($relative === '.git' || str_starts_with($relative, '.git/')) {
                continue;
            }
            if ($entry->isLink()) {
                throw new RuntimeException("Symlinks are not allowed inside the curated package: $relative");
            }
            if ($entry->isFile() && !isset($allowed[$relative])) {
                throw new RuntimeException("Unexpected file in curated destination: $relative. Review and migrate it explicitly; pack never deletes it.");
            }
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

    private static function lintPhp(string $file): void
    {
        $p = proc_open([PHP_BINARY, '-l', $file], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        if (!is_resource($p)) {
            throw new RuntimeException('Could not start PHP syntax validation.');
        }
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        if (proc_close($p) !== 0) {
            throw new RuntimeException("PHP syntax validation failed for $file: $out $err");
        }
    }

    private static function absolutePath(string $path): string
    {
        if (preg_match('~^(?:[A-Za-z]:[\\\\/]|/|\\\\\\\\)~', $path)) {
            return rtrim($path, '\\/');
        }
        return rtrim(getcwd() . DIRECTORY_SEPARATOR . $path, '\\/');
    }

    private static function removeOwnedStage(string $path): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $entry) {
            $entry->isDir() ? rmdir($entry->getPathname()) : unlink($entry->getPathname());
        }
        rmdir($path);
    }
}
