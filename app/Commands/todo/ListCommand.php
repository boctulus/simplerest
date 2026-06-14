<?php

require_once __DIR__ . '/BaseTodoCommand.php';

class TodoListCommand extends BaseTodoCommand
{
    public function __construct()
    {
        $this->command     = 'list';
        $this->description = 'Lista los documentos de tarea en docs/to-do/';
        $this->aliases     = ['ls'];
        $this->examples    = [
            'php com todo list',
            'php com todo list --all',
            'php com todo list --unparsed',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => ['all', 'unparsed'],
            'options'  => [],
        ];
    }

    public function execute(array $parsed): void
    {
        $showAll    = $parsed['all'] ?? false;
        $unparsed   = $parsed['unparsed'] ?? false;
        $base       = $this->getTodoDir();
        $folders    = $this->detectAllFolders();

        $stateNames = [
            'pending'      => '📋 Pendientes',
            'in-progress'  => '🔄 En Progreso',
            'done'         => '✅ Completadas',
            'on-hold'      => '⏸ En Espera',
            'needs-review' => '👀 Revisión',
            'archived'     => '📦 Archivo',
            'abandoned'    => '🗑 Abandonadas',
        ];

        $found = false;

        foreach ($folders as $state => $dir) {
            if (!$showAll && $state === 'abandoned') {
                continue;
            }
            if (!$showAll && $state === 'archived') {
                continue;
            }

            $files = glob($dir . '/*.md');
            if (empty($files)) {
                continue;
            }

            $found = true;
            echo "\n" . ($stateNames[$state] ?? $state) . ":\n";
            echo str_repeat('─', 50) . "\n";

            sort($files);

            foreach ($files as $file) {
                $basename = basename($file);

                if ($unparsed) {
                    echo "  {$basename}\n";
                    continue;
                }

                $parsed = $this->readFileMeta($file);
                if ($parsed && isset($parsed['meta']['title'])) {
                    $title      = $parsed['meta']['title'];
                    $step       = $parsed['meta']['current_step'] ?? '—';
                    $complexity = $parsed['meta']['global_complexity'] ?? '—';
                    $tagsPart   = '';
                    if (!empty($parsed['meta']['tags'])) {
                        $tagsPart = ' [' . implode(', ', (array)$parsed['meta']['tags']) . ']';
                    }
                    echo "  {$basename}\n";
                    echo "    → {$title}{$tagsPart}\n";
                    echo "    Paso: {$step} | Complejidad: {$complexity}\n";
                } else {
                    echo "  {$basename} (sin frontmatter)\n";
                }
            }
        }

        if (!$found) {
            echo "No hay tareas en docs/to-do/\n";
        }

        echo "\n";
    }
}
