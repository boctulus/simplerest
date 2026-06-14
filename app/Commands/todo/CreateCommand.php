<?php

require_once __DIR__ . '/BaseTodoCommand.php';

class TodoCreateCommand extends BaseTodoCommand
{
    public function __construct()
    {
        $this->command     = 'create';
        $this->description = 'Crea un nuevo documento de tarea en docs/to-do/';
        $this->aliases     = [];
        $this->examples    = [
            'php com todo create "Mi nueva tarea"',
            'php com todo create "Refactor auth" --complexity=high --tags="backend,security"',
            'php com todo create "Testing" --title="Test Suite" --current-step="Setup"',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['name'],
            'flags'    => ['for-agents'],
            'options'  => [
                'name'           => ['describe' => 'Nombre de la tarea (primer argumento posicional o --name)', 'default' => null],
                'title'          => ['describe' => 'Título de la tarea (default: mismo que name)', 'default' => null],
                'current-step'   => ['describe' => 'Paso actual', 'default' => 'Por definir'],
                'complexity'     => ['describe' => 'Complejidad: trivial|low|medium|high|extreme', 'default' => 'medium'],
                'tags'           => ['describe' => 'Tags separados por coma', 'default' => null],
            ],
        ];
    }

    public function validate(array $parsed): bool
    {
        $name = $parsed['_positional'][0] ?? $parsed['name'] ?? null;
        if (!$name) {
            echo "✗ Error: Debes proporcionar un nombre para la tarea.\n";
            $this->showUsage();
            return false;
        }
        return true;
    }

    public function execute(array $parsed): void
    {
        $name       = $parsed['_positional'][0] ?? $this->opt($parsed, 'name');
        $filename   = $this->toKebabCase($name) . '.md';
        $filepath   = $this->getTodoDir() . '/' . $filename;

        if (file_exists($filepath)) {
            $this->log("Ya existe una tarea con el nombre '$filename'", 'error');
            return;
        }

        $title       = $this->opt($parsed, 'title', $name);
        $step        = $this->opt($parsed, 'current-step', 'Por definir');
        $complexity  = $this->opt($parsed, 'complexity', 'medium');
        $forAgents   = $parsed['for_agents'] ?? false;
        $tagsRaw     = $this->opt($parsed, 'tags');

        $validComplexities = ['trivial', 'low', 'medium', 'high', 'extreme'];
        if (!in_array($complexity, $validComplexities)) {
            $this->log("Complejidad inválida: '$complexity'. Valores: " . implode('|', $validComplexities), 'error');
            return;
        }

        $meta = [
            'title'                 => $title,
            'current_step'          => $step,
            'next_step'             => null,
            'parallelizable_steps'  => [],
            'parent'                => null,
            'global_complexity'     => $complexity,
            'for_agents'            => $forAgents,
            'next_step_complexity'  => null,
        ];

        if ($tagsRaw) {
            $meta['tags'] = array_map('trim', explode(',', $tagsRaw));
        }

        $body = "## Pasos planificados\n\n1. **$step**\n";

        $this->writeFileWithMeta($filepath, $meta, $body);
        $this->log("Tarea creada: docs/to-do/$filename", 'success');
    }
}
