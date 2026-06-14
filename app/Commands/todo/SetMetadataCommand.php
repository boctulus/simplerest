<?php

require_once __DIR__ . '/BaseTodoCommand.php';

class TodoSetMetadataCommand extends BaseTodoCommand
{
    public function __construct()
    {
        $this->command     = 'set-metadata';
        $this->description = 'Agrega o actualiza el frontmatter de una tarea Kanban en docs/to-do/';
        $this->aliases     = ['meta'];
        $this->examples    = [
            'php com todo set-metadata --file=test-frontmatter.md --title="Mi tarea" --current-step="Analisis" --global-complexity=low',
            'php com todo set-metadata --file=ecommerce-roadmap-progress.md --title="E-Commerce Roadmap" --global-complexity=extreme',
            'php com todo set-metadata --file=test-frontmatter.md --tags="backend,test" --parent=null',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['file'],
            'optional' => [],
            'flags'    => ['for-agents'],
            'options'  => [
                'file'                   => ['describe' => 'Ruta al archivo de tarea en docs/to-do/'],
                'title'                  => ['describe' => 'Nuevo título de la tarea'],
                'current-step'           => ['describe' => 'Nuevo paso actual'],
                'next-step'              => ['describe' => 'Siguiente paso planificado'],
                'next-step-complexity'   => ['describe' => 'Complejidad del siguiente paso: trivial|low|medium|high|extreme'],
                'global-complexity'      => ['describe' => 'Complejidad global: trivial|low|medium|high|extreme'],
                'tags'                   => ['describe' => 'Tags separados por coma'],
                'parent'                 => ['describe' => 'Tarea padre (o null)'],
                'parallelizable-steps'   => ['describe' => 'Pasos paralelizables separados por coma'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $fileRef = $this->opt($parsed, 'file');
        $absPath = $this->resolveFilePath($fileRef);

        if ($absPath === null) {
            $this->log("Archivo no encontrado: $fileRef", 'error');
            return;
        }

        $updates = [];

        if (($v = $this->opt($parsed, 'title')) !== null) {
            $updates['title'] = $v;
        }
        if (($v = $this->opt($parsed, 'current-step')) !== null) {
            $updates['current_step'] = $v;
        }
        if (($v = $this->opt($parsed, 'next-step')) !== null) {
            $updates['next_step'] = $v === 'null' ? null : $v;
        }
        if (($v = $this->opt($parsed, 'next-step-complexity')) !== null) {
            $updates['next_step_complexity'] = $v === 'null' ? null : $v;
        }
        if (($v = $this->opt($parsed, 'global-complexity')) !== null) {
            $validComplexities = ['trivial', 'low', 'medium', 'high', 'extreme'];
            if (!in_array($v, $validComplexities)) {
                $this->log("Complejidad inválida: '$v'. Valores: " . implode('|', $validComplexities), 'error');
                return;
            }
            $updates['global_complexity'] = $v;
        }
        if (($v = $this->opt($parsed, 'tags')) !== null) {
            $updates['tags'] = array_map('trim', explode(',', $v));
        }
        if (($v = $this->opt($parsed, 'parent')) !== null) {
            $updates['parent'] = $v === 'null' ? null : $v;
        }
        if (($v = $this->opt($parsed, 'parallelizable-steps')) !== null) {
            $updates['parallelizable_steps'] = array_map('trim', explode(',', $v));
        }
        if (isset($parsed['for_agents'])) {
            $updates['for_agents'] = $parsed['for_agents'];
        }

        if (empty($updates)) {
            $this->log("No se especificaron metadatos para actualizar", 'warning');
            return;
        }

        $this->updateFileMeta($absPath, $updates);
        $relPath = substr($absPath, strlen(realpath($this->getTodoDir())) + 1);
        $this->log("Metadatos actualizados: docs/to-do/$relPath", 'success');
    }
}
