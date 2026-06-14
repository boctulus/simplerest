<?php

require_once __DIR__ . '/BaseTodoCommand.php';

class TodoShowCommand extends BaseTodoCommand
{
    public function __construct()
    {
        $this->command     = 'show';
        $this->description = 'Muestra el contenido de un documento de tarea';
        $this->aliases     = ['cat', 'view'];
        $this->examples    = [
            'php com todo show --file=dominio-com.md',
            'php com todo show --file=in-progress/pricing-promotion-engine.md',
            'php com todo show --file=dominio-com.md --raw',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['file'],
            'optional' => [],
            'flags'    => ['raw'],
            'options'  => [
                'file' => ['describe' => 'Ruta relativa al archivo en docs/to-do/'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $fileRef   = $parsed['_positional'][0] ?? $this->opt($parsed, 'file');
        $raw       = $parsed['raw'] ?? false;

        if (!$fileRef) {
            $this->log("Debes especificar --file=<ruta>", 'error');
            return;
        }

        $absPath = $this->resolveFilePath($fileRef);
        if ($absPath === null) {
            $this->log("Archivo no encontrado: $fileRef", 'error');
            return;
        }

        if ($raw) {
            echo file_get_contents($absPath) . "\n";
            return;
        }

        $parsed = $this->readFileMeta($absPath);
        if ($parsed === null) {
            $this->log("No se pudo leer el archivo", 'error');
            return;
        }

        $meta = $parsed['meta'];
        $body = $parsed['body'];

        $state = $this->getStateFromPath($absPath);

        echo "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "  {$meta['title']}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "  Archivo:  " . basename($absPath) . "\n";
        echo "  Estado:   {$state}\n";
        echo "  Paso:     " . ($meta['current_step'] ?? '—') . "\n";
        echo "  Siguiente: " . ($meta['next_step'] ?? '—') . "\n";
        echo "  Complejidad: " . ($meta['global_complexity'] ?? '—') . "\n";
        echo "  Para agentes: " . (!empty($meta['for_agents']) ? 'Sí' : 'No') . "\n";
        if (!empty($meta['tags'])) {
            echo "  Tags:     " . implode(', ', (array)$meta['tags']) . "\n";
        }
        if (!empty($meta['parent'])) {
            echo "  Padre:    " . $meta['parent'] . "\n";
        }
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "\n";
        echo $body . "\n";
    }
}
