<?php
declare(strict_types=1);

/**
 * Block audit-methodology statements in public documentation and skills.
 * Usage: php scripts/documentation-publication-gate.php [--root PATH]
 */

function documentationPaths(string $root): array
{
    $paths = [];
    foreach (['docs', 'docs-site', '.agents/skills', '.agent/skills'] as $directory) {
        $absolute = $root . DIRECTORY_SEPARATOR . $directory;
        if (!is_dir($absolute)) {
            continue;
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($absolute, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if (!$file->isFile() || strtolower($file->getExtension()) !== 'md') {
                continue;
            }
            $path = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
            if (preg_match('~^docs/(audit|_internal|issues|to-do|framework)/~', $path)
                || preg_match('~(?:^|/)[^/]*-audit\.md$~i', $path)
                || $path === 'docs/documentation-governance.md') {
                continue;
            }
            if ((str_starts_with($path, '.agent/skills/') || str_starts_with($path, '.agents/skills/'))
                && basename($path) !== 'SKILL.md') {
                continue;
            }
            $paths[] = $path;
        }
    }
    sort($paths);
    return $paths;
}

function publicationPatterns(): array
{
    return [
        'audit-methodology-es' => '/\b(?:no se (?:ejecut[oó]|ejecutaron|prob[oó]|probaron|verific[oó]|verificaron|reprodujo|reprodujeron|inspeccion[oó]|inspeccionaron)|no se ha(?:n)? (?:ejecutado|probado|verificado|reproducido))\b/iu',
        'audit-methodology-en' => '/\b(?:was|were) not (?:run|tested|verified|executed|reproduced)\b|\b(?:not run|not reproduced|not tested|not verified)\b/i',
        'audit-disclaimer' => '/\b(?:it (?:does not|did not) (?:establish|demonstrate|prove)|(?:no|tampoco) (?:demuestra|acredita) que|(?:source|code)[ -]traced|inspected but not run)\b/iu',
        'public-validation-status' => '/^status:\s*(?:partial|unverified|audit-only)\s*$/i',
    ];
}

function checkDocumentation(string $root): array
{
    $findings = [];
    foreach (documentationPaths($root) as $path) {
        $contents = file_get_contents($root . DIRECTORY_SEPARATOR . $path);
        if ($contents === false) {
            throw new RuntimeException('Cannot read ' . $path);
        }
        $codeFence = false;
        foreach (preg_split('/\r\n|\r|\n/', $contents) as $index => $line) {
            if (preg_match('/^\s*(\x60{3}|~~~)/', $line)) {
                $codeFence = !$codeFence;
                continue;
            }
            if ($codeFence) {
                continue;
            }
            foreach (publicationPatterns() as $rule => $pattern) {
                if (preg_match($pattern, $line)) {
                    $findings[] = sprintf('%s:%d [%s] %s', $path, $index + 1, $rule, trim(substr($line, 0, 180)));
                    break;
                }
            }
        }
    }
    return $findings;
}

$args = array_slice($argv, 1);
$root = dirname(__DIR__);
if ($args !== []) {
    if (count($args) !== 2 || $args[0] !== '--root' || !is_dir($args[1])) {
        fwrite(STDERR, "Usage: php scripts/documentation-publication-gate.php [--root PATH]\n");
        exit(2);
    }
    $root = (string) realpath($args[1]);
}

$findings = checkDocumentation($root);
if ($findings !== []) {
    fwrite(STDERR, implode("\n", $findings) . "\n");
    fwrite(STDERR, sprintf(
        "Blocked: %d audit-language finding(s). Keep functional warnings in the guide; move audit execution history to docs/audit/*-audit.md.\n",
        count($findings)
    ));
    exit(1);
}
fwrite(STDOUT, "Public documentation and skills contain no detected audit-only phrases.\n");
