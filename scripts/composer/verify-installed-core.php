<?php

declare(strict_types=1);

/**
 * VERIFY-only gate for a Composer-installed core candidate.
 *
 * This script does not build, publish, tag, or update a consumer dependency.
 */

$options = getopt('', ['installed:', 'provenance:']);
$installed = isset($options['installed']) ? realpath((string) $options['installed']) : false;
$provenancePath = isset($options['provenance']) ? realpath((string) $options['provenance']) : false;

try {
    if ($installed === false || !is_dir($installed)) {
        throw new RuntimeException('Provide --installed=<Composer package directory>.');
    }
    if ($provenancePath === false || !is_file($provenancePath)) {
        throw new RuntimeException('Provide --provenance=<release-provenance.json>.');
    }

    $provenance = json_decode((string) file_get_contents($provenancePath), true, 512, JSON_THROW_ON_ERROR);
    if (($provenance['schema'] ?? null) !== 1
        || ($provenance['package'] ?? null) !== 'boctulus/simplerest'
        || !preg_match('/^[a-f0-9]{40}$/D', (string) ($provenance['canonical_source_commit'] ?? ''))
        || !preg_match('/^[a-f0-9]{40}$/D', (string) ($provenance['package_commit'] ?? ''))
        || !is_array($provenance['files_sha256'] ?? null)) {
        throw new RuntimeException('Release provenance is incomplete or invalid.');
    }

    $expected = $provenance['files_sha256'];
    ksort($expected);
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($installed, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $entry) {
        $absolute = str_replace('\\', '/', $entry->getPathname());
        if (!$entry->isFile() || str_contains($absolute, '/.git/')) {
            continue;
        }
        $relative = substr($absolute, strlen(str_replace('\\', '/', $installed)) + 1);
        if (str_starts_with($relative, 'scripts/pack/')
            || str_starts_with($relative, 'app/Commands/pack/')
            || $relative === 'scripts/pack_framework.php'
            || $relative === 'com') {
            throw new RuntimeException("PACK implementation leaked into the installed package: $relative");
        }
        $files[$relative] = $entry->getPathname();
    }
    ksort($files);

    if (array_keys($files) !== array_keys($expected)) {
        $missing = array_values(array_diff(array_keys($expected), array_keys($files)));
        $unexpected = array_values(array_diff(array_keys($files), array_keys($expected)));
        throw new RuntimeException(
            'Installed Composer package file set differs from release provenance. Missing: '
            . implode(', ', $missing) . '; unexpected: ' . implode(', ', $unexpected)
        );
    }

    $actual = [];
    if (is_dir($installed . DIRECTORY_SEPARATOR . '.git') || is_file($installed . DIRECTORY_SEPARATOR . '.git')) {
        $head = runGit($installed, ['rev-parse', 'HEAD^{commit}']);
        if (!hash_equals((string) $provenance['package_commit'], trim($head))) {
            throw new RuntimeException('Composer source install does not point at the provenance package commit.');
        }

        $status = runGit($installed, ['status', '--porcelain=v1', '--untracked-files=all']);
        if (trim($status) !== '') {
            throw new RuntimeException("Composer source install is dirty:\n" . trim($status));
        }

        $treePaths = preg_split('/\r?\n/', trim(runGit($installed, ['ls-tree', '-r', '--name-only', 'HEAD']))) ?: [];
        sort($treePaths, SORT_STRING);
        if ($treePaths !== array_keys($expected)) {
            throw new RuntimeException('Composer source Git tree differs from release provenance.');
        }

        foreach (array_keys($expected) as $path) {
            $actual[$path] = hash('sha256', runGit($installed, ['show', 'HEAD:' . $path]));
        }
    } else {
        foreach ($files as $path => $absolute) {
            $actual[$path] = strtolower((string) hash_file('sha256', $absolute));
        }
    }

    foreach ($expected as $path => $hash) {
        if (!is_string($hash) || !hash_equals(strtolower($hash), $actual[$path])) {
            throw new RuntimeException("Installed Composer package hash differs from release provenance: $path");
        }
    }

    fwrite(
        STDOUT,
        sprintf(
            "PASS installed boctulus/simplerest: %d files; source=%s package=%s version=%s\n",
            count($actual),
            $provenance['canonical_source_commit'],
            $provenance['package_commit'],
            $provenance['intended_version'] ?? 'unknown'
        )
    );
    exit(0);
} catch (Throwable $error) {
    fwrite(STDERR, $error->getMessage() . PHP_EOL);
    exit(1);
}

/**
 * Read immutable Git metadata or blob bytes without invoking a command shell.
 *
 * @param list<string> $arguments
 */
function runGit(string $workingTree, array $arguments): string
{
    $command = array_merge(['git', '-C', $workingTree], $arguments);
    $pipes = [];
    $process = proc_open($command, [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ], $pipes);
    if (!is_resource($process)) {
        throw new RuntimeException('Could not start Git while verifying the Composer source install.');
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);
    if ($exitCode !== 0) {
        throw new RuntimeException('Git verification failed: ' . trim((string) $stderr));
    }

    return (string) $stdout;
}
