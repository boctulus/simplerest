<?php

declare(strict_types=1);

const MANIFEST_PATH = 'docs/canonical-manifest.json';
const CONTROL_PATHS = [
    MANIFEST_PATH,
    'scripts/documentation-governance-guard.php',
    '.github/workflows/documentation-governance.yml',
];

function usage(): string
{
    return <<<USAGE
Usage: php scripts/documentation-governance-guard.php [options]

Options:
  --base REF                       Compare a branch or commit to the local HEAD.
  --head REF                       Pair with --base to select the proposed head.
  --event-file PATH                GitHub pull_request event JSON for label approval.
  --approval-label LABEL           Maintainer approval label to accept.
  --approved-breaking-change TEXT  Local override, only after maintainer approval.
  --help                           Show this help.

With no options, compares the working tree (including staged edits) to HEAD.
USAGE;
}

function parseOptions(array $arguments): array
{
    $options = [
        'base' => null,
        'head' => null,
        'event-file' => null,
        'approval-label' => null,
        'approved-breaking-change' => null,
        'help' => false,
    ];

    for ($i = 0; $i < count($arguments); $i++) {
        $argument = $arguments[$i];
        if ($argument === '--help') {
            $options['help'] = true;
            continue;
        }

        if (strpos($argument, '--') !== 0) {
            throw new InvalidArgumentException('Unexpected argument: ' . $argument);
        }

        $parts = explode('=', substr($argument, 2), 2);
        $name = $parts[0];
        if (!array_key_exists($name, $options) || $name === 'help') {
            throw new InvalidArgumentException('Unknown option: --' . $name);
        }

        if (count($parts) === 2) {
            $value = $parts[1];
        } else {
            $i++;
            if (!isset($arguments[$i]) || strpos($arguments[$i], '--') === 0) {
                throw new InvalidArgumentException('Missing value for --' . $name);
            }
            $value = $arguments[$i];
        }

        $options[$name] = $value;
    }

    if ($options['head'] !== null && $options['base'] === null) {
        throw new InvalidArgumentException('--head requires --base.');
    }

    return $options;
}

function runCommand(array $command, string $workingDirectory): array
{
    $pipes = [];
    $process = proc_open(
        $command,
        [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
        $pipes,
        $workingDirectory,
        null,
        ['bypass_shell' => true]
    );

    if (!is_resource($process)) {
        throw new RuntimeException('Could not start command: ' . implode(' ', $command));
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    return [$exitCode, $stdout === false ? '' : $stdout, $stderr === false ? '' : $stderr];
}

function runGit(array $arguments, string $workingDirectory): array
{
    return runCommand(array_merge(['git'], $arguments), $workingDirectory);
}

function parseNameStatus(string $output): array
{
    $tokens = explode("\0", trim($output, "\0"));
    $changes = [];

    for ($i = 0; $i < count($tokens);) {
        $status = $tokens[$i++];
        if ($status === '') {
            continue;
        }

        $source = $tokens[$i++] ?? '';
        $destination = null;
        if ($status[0] === 'R' || $status[0] === 'C') {
            $destination = $tokens[$i++] ?? '';
        }

        $changes[] = [
            'status' => $status,
            'source' => str_replace('\\', '/', $source),
            'destination' => $destination === null ? null : str_replace('\\', '/', $destination),
        ];
    }

    return $changes;
}

function lineCount(string $contents): int
{
    if ($contents === '') {
        return 0;
    }

    return substr_count($contents, "\n") + (substr($contents, -1) === "\n" ? 0 : 1);
}

function numstatDeletions(string $revisionSpec, string $path, string $root): int
{
    [$exitCode, $output, $error] = runGit(
        ['diff', '--numstat', '--no-renames', $revisionSpec, '--', $path],
        $root
    );
    if ($exitCode !== 0) {
        throw new RuntimeException('Could not inspect diff for ' . $path . ': ' . trim($error));
    }

    foreach (preg_split('/\r?\n/', trim($output)) as $line) {
        $columns = explode("\t", $line, 3);
        if (count($columns) >= 3 && $columns[2] === $path) {
            return ctype_digit($columns[1]) ? (int) $columns[1] : 0;
        }
    }

    return 0;
}

function hasApproval(array $options): ?string
{
    if ($options['approved-breaking-change'] !== null && trim($options['approved-breaking-change']) !== '') {
        return 'local approval evidence: ' . trim($options['approved-breaking-change']);
    }

    if ($options['event-file'] === null || $options['approval-label'] === null) {
        return null;
    }

    $contents = file_get_contents($options['event-file']);
    if ($contents === false) {
        throw new RuntimeException('Could not read GitHub event file.');
    }

    $event = json_decode($contents, true);
    if (!is_array($event)) {
        throw new RuntimeException('GitHub event file is not valid JSON.');
    }

    $labels = $event['pull_request']['labels'] ?? [];
    foreach ($labels as $label) {
        if (($label['name'] ?? null) === $options['approval-label']) {
            return 'maintainer approval label: ' . $options['approval-label'];
        }
    }

    return null;
}

try {
    $options = parseOptions(array_slice($argv, 1));
    if ($options['help']) {
        fwrite(STDOUT, usage() . PHP_EOL);
        exit(0);
    }

    $rootResult = runCommand(['git', 'rev-parse', '--show-toplevel'], getcwd());
    if ($rootResult[0] !== 0) {
        throw new RuntimeException('Run this guard inside a Git worktree.');
    }

    $root = trim($rootResult[1]);
    $manifestRef = $options['base'] ?? 'HEAD';
    $headRef = $options['head'] ?? 'HEAD';

    [$manifestExit, $manifestContents, $manifestError] = runGit(
        ['show', $manifestRef . ':' . MANIFEST_PATH],
        $root
    );
    $bootstrap = $manifestExit !== 0;
    if ($bootstrap) {
        $manifestFile = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, MANIFEST_PATH);
        $manifestContents = is_file($manifestFile) ? file_get_contents($manifestFile) : false;
    }
    if ($manifestContents === false) {
        throw new RuntimeException('Canonical manifest is unavailable: ' . trim($manifestError));
    }

    $manifest = json_decode($manifestContents, true);
    if (!is_array($manifest) || !isset($manifest['canonical_files']) || !is_array($manifest['canonical_files'])) {
        throw new RuntimeException('Canonical manifest must define a canonical_files array.');
    }

    $canonicalFiles = [];
    foreach ($manifest['canonical_files'] as $path) {
        if (!is_string($path) || strpos($path, 'docs/') !== 0 || strpos($path, '..') !== false) {
            throw new RuntimeException('Invalid canonical path in manifest.');
        }
        $canonicalFiles[] = $path;
    }
    $canonicalFiles = array_values(array_unique($canonicalFiles));
    $canonicalSet = array_fill_keys($canonicalFiles, true);

    if ($options['base'] !== null) {
        $comparisonSpecs = [$options['base'] . '...' . $headRef, 'HEAD'];
        [$mergeBaseExit, $mergeBase, $mergeBaseError] = runGit(
            ['merge-base', $options['base'], $headRef],
            $root
        );
        if ($mergeBaseExit !== 0) {
            throw new RuntimeException('Could not find comparison base: ' . trim($mergeBaseError));
        }
        $baselineRevision = trim($mergeBase);
    } else {
        $comparisonSpecs = ['HEAD'];
        $baselineRevision = 'HEAD';
    }

    $changes = [];
    foreach ($comparisonSpecs as $revisionSpec) {
        [$exitCode, $output, $error] = runGit(
            ['diff', '--name-status', '-z', '--find-renames=50%', $revisionSpec],
            $root
        );
        if ($exitCode !== 0) {
            throw new RuntimeException('Could not inspect ' . $revisionSpec . ': ' . trim($error));
        }
        $changes[$revisionSpec] = parseNameStatus($output);
    }

    $violations = [];
    $modifiedCanonical = [];
    $controlSet = array_fill_keys(CONTROL_PATHS, true);

    foreach ($changes as $revisionSpec => $items) {
        foreach ($items as $change) {
            $statusType = $change['status'][0];
            $source = $change['source'];
            $destination = $change['destination'];
            $sourceIsCanonical = isset($canonicalSet[$source]);
            $destinationIsCanonical = $destination !== null && isset($canonicalSet[$destination]);

            if ($statusType === 'D' && ($sourceIsCanonical || isset($controlSet[$source]))) {
                $violations[] = 'Deletion of protected path: ' . $source;
            } elseif (($statusType === 'R' || $statusType === 'C')
                && ($sourceIsCanonical || $destinationIsCanonical || isset($controlSet[$source])
                    || ($destination !== null && isset($controlSet[$destination])))) {
                $violations[] = 'Rename or copy involving protected path: ' . $source . ' -> ' . $destination;
            } elseif (!$bootstrap && isset($controlSet[$source]) && $statusType !== 'A') {
                $violations[] = 'Governance control file changed and requires maintainer approval: ' . $source;
            }

            if ($sourceIsCanonical && ($statusType === 'M' || $statusType === 'T')) {
                $modifiedCanonical[$source] = true;
            }
        }
    }

    foreach ($canonicalFiles as $path) {
        $absolutePath = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
        if (!is_file($absolutePath)) {
            $violations[] = 'Canonical file is missing from the working tree: ' . $path;
        }
    }

    $rewriteRules = $manifest['rewrite_guard'] ?? [];
    $minimumDeletedLines = (int) ($rewriteRules['minimum_deleted_lines'] ?? 8);
    $minimumDeletedRatio = (float) ($rewriteRules['minimum_deleted_ratio'] ?? 0.35);

    foreach (array_keys($modifiedCanonical) as $path) {
        $deletedLines = 0;
        foreach (array_keys($changes) as $revisionSpec) {
            $deletedLines += numstatDeletions($revisionSpec, $path, $root);
        }

        [$oldExit, $oldContents, $oldError] = runGit(
            ['show', $baselineRevision . ':' . $path],
            $root
        );
        if ($oldExit !== 0) {
            continue;
        }

        $oldLines = lineCount($oldContents);
        $deletedRatio = $oldLines > 0 ? $deletedLines / $oldLines : 0.0;
        $effectiveMinimumDeletedLines = $oldLines > 0
            ? min($minimumDeletedLines, max(1, (int) ceil($oldLines * $minimumDeletedRatio)))
            : $minimumDeletedLines;
        if ($deletedLines >= $effectiveMinimumDeletedLines && $deletedRatio >= $minimumDeletedRatio) {
            $violations[] = sprintf(
                'Substantial removal from %s: %d of %d prior lines deleted (%.0f%%; threshold %d lines and %.0f%%).',
                $path,
                $deletedLines,
                $oldLines,
                $deletedRatio * 100,
                $effectiveMinimumDeletedLines,
                $minimumDeletedRatio * 100
            );
        }
    }

    $approval = hasApproval($options);
    if (count($violations) > 0 && $approval !== null) {
        fwrite(STDOUT, 'APPROVED OVERRIDE (' . $approval . ')' . PHP_EOL);
        foreach (array_unique($violations) as $violation) {
            fwrite(STDOUT, '  - ' . $violation . PHP_EOL);
        }
        exit(0);
    }

    if (count($violations) > 0) {
        fwrite(STDERR, 'Documentation governance guard blocked this change:' . PHP_EOL);
        foreach (array_unique($violations) as $violation) {
            fwrite(STDERR, '  - ' . $violation . PHP_EOL);
        }
        fwrite(STDERR, 'Obtain explicit maintainer approval. For a pull request, use the label docs-governance-approved.' . PHP_EOL);
        fwrite(STDERR, 'For an already-approved local change, pass --approved-breaking-change="approval reference".' . PHP_EOL);
        exit(1);
    }

    fwrite(STDOUT, 'Documentation governance guard passed (' . count($canonicalFiles) . ' canonical files checked).' . PHP_EOL);
    exit(0);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Documentation governance guard error: ' . $exception->getMessage() . PHP_EOL);
    exit(2);
}
