<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\libs\Documentor;

class FromJsonCommand extends BaseCommand
{
    public function __construct()
    {
        $this->command     = 'from-json';
        $this->description = 'Convierte un archivo JSON de documentación a Markdown';
        $this->aliases     = ['json2md'];
        $this->examples    = [
            'php com doc from-json documentation_ej1.json',
            'php com doc from-json /path/to/docs/api.json',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['path'],
            'optional' => [],
            'flags'    => [],
            'options'  => [
                'path' => ['describe' => 'Ruta al archivo JSON (o primer arg posicional)'],
            ],
        ];
    }

    public function validate(array $parsed): bool
    {
        // Accept path as positional or --path
        $path = $parsed['_positional'][0] ?? $parsed['path'] ?? null;
        if (!$path) {
            echo "✗ Error: se requiere la ruta al archivo JSON.\n";
            $this->showUsage();
            return false;
        }
        return true;
    }

    public function execute(array $parsed): void
    {
        $path   = $parsed['_positional'][0] ?? $this->opt($parsed, 'path');
        $result = Documentor::fromJSONFileToMarkDown($path);
        echo $result . "\n";
    }
}
