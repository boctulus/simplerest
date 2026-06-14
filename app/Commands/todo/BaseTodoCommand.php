<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

abstract class BaseTodoCommand extends BaseCommand
{
    protected function getTodoDir(): string
    {
        return ROOT_PATH . '/docs/to-do';
    }

    protected function resolveFilePath(string $file): ?string
    {
        $base = $this->getTodoDir();
        $path = $base . '/' . ltrim($file, '/');

        if (file_exists($path)) {
            return realpath($path);
        }

        $candidates = [
            $base . '/' . $file,
            $base . '/in-progress/' . $file,
            $base . '/done/' . $file,
            $base . '/on-hold/' . $file,
            $base . '/needs-review/' . $file,
            $base . '/archived/' . $file,
            $base . '/abandoned/' . $file,
        ];

        foreach ($candidates as $c) {
            if (file_exists($c)) {
                return realpath($c);
            }
        }

        return null;
    }

    protected function parseFrontmatter(string $content): array
    {
        $meta = [];

        if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)$/s', $content, $m)) {
            $raw  = $m[1];
            $body = $m[2];

            foreach (explode("\n", $raw) as $line) {
                if (preg_match('/^(\w+):\s*(.*)$/', $line, $parts)) {
                    $key   = $parts[1];
                    $value = trim($parts[2]);

                    if (in_array($value, ['true', 'false'], true)) {
                        $value = $value === 'true';
                    } elseif ($value === 'null') {
                        $value = null;
                    } elseif (preg_match('/^\[.*\]$/', $value)) {
                        $inner = trim($value, '[]');
                        $value = $inner !== '' ? array_map('trim', explode(',', $inner)) : [];
                    } elseif (preg_match('/^"\s*(.*)\s*"$/', $value, $qm)) {
                        $value = $qm[1];
                    }

                    $meta[$key] = $value;
                }
            }

            return ['meta' => $meta, 'body' => trim($body)];
        }

        return ['meta' => [], 'body' => trim($content)];
    }

    protected function buildFrontmatter(array $meta): string
    {
        $lines = ["---"];
        foreach ($meta as $key => $value) {
            if (is_bool($value)) {
                $lines[] = "$key: " . ($value ? 'true' : 'false');
            } elseif (is_null($value)) {
                $lines[] = "$key: null";
            } elseif (is_array($value)) {
                $items = implode(', ', array_map('trim', $value));
                $lines[] = "$key: [$items]";
            } elseif (str_contains($value, ':') || str_contains($value, '#')) {
                $lines[] = "$key: \"$value\"";
            } else {
                $lines[] = "$key: $value";
            }
        }
        $lines[] = "---";
        return implode("\n", $lines) . "\n";
    }

    protected function toKebabCase(string $str): string
    {
        $str = preg_replace('/[^a-zA-Z0-9\s-]/', '', $str);
        $str = preg_replace('/\s+/', '-', trim($str));
        return strtolower($str);
    }

    protected function readFileMeta(string $path): ?array
    {
        if (!file_exists($path)) {
            return null;
        }
        $content = file_get_contents($path);
        return $this->parseFrontmatter($content);
    }

    protected function writeFileWithMeta(string $path, array $meta, string $body): void
    {
        $content = $this->buildFrontmatter($meta) . $body . "\n";
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($path, $content);
    }

    protected function updateFileMeta(string $path, array $updates): void
    {
        $parsed = $this->readFileMeta($path);
        if ($parsed === null) {
            $this->log("No se pudo leer el archivo: $path", 'error');
            return;
        }

        foreach ($updates as $key => $value) {
            if ($value !== null) {
                $parsed['meta'][$key] = $value;
            } else {
                unset($parsed['meta'][$key]);
            }
        }

        $this->writeFileWithMeta($path, $parsed['meta'], $parsed['body']);
    }

    protected function getStateFromPath(string $absPath): string
    {
        $base   = $this->getTodoDir();
        $rel    = substr(realpath($absPath), strlen(realpath($base)));
        $rel    = ltrim(str_replace('\\', '/', $rel), '/');
        $parts  = explode('/', $rel);

        if (count($parts) > 1) {
            return $parts[0];
        }
        return 'pending';
    }

    protected function detectStateFolder(string $file, ?string &$outFolder): ?string
    {
        $base  = $this->getTodoDir();
        $stateFolders = ['in-progress', 'done', 'on-hold', 'needs-review', 'archived', 'abandoned'];

        foreach ($stateFolders as $folder) {
            $path = $base . '/' . $folder . '/' . $file;
            if (file_exists($path)) {
                $outFolder = $folder;
                return realpath($path);
            }
        }

        $path = $base . '/' . $file;
        if (file_exists($path)) {
            $outFolder = 'pending';
            return realpath($path);
        }

        return null;
    }

    protected function detectAllFolders(): array
    {
        $base = $this->getTodoDir();
        $folders = [
            'pending'       => $base,
            'in-progress'   => $base . '/in-progress',
            'done'          => $base . '/done',
            'on-hold'       => $base . '/on-hold',
            'needs-review'  => $base . '/needs-review',
            'archived'      => $base . '/archived',
            'abandoned'     => $base . '/abandoned',
        ];

        $result = [];
        foreach ($folders as $state => $dir) {
            if (is_dir($dir)) {
                $result[$state] = $dir;
            }
        }
        return $result;
    }
}
