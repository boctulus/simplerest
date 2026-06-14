<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeCssScanCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'css-scan';
        $this->description = 'Escanea CSS y genera resumen';
        $this->aliases     = [];
        $this->examples    = [
            'php com make css-scan --dir=/path/to/css',
            'php com make css-scan --dir=/path/to/css --relative=yes',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => ['dir'],
            'optional' => ['relative'],
            'flags'    => [],
            'options'  => [
                'dir'      => ['describe' => 'Directorio con archivos CSS'],
                'relative' => ['describe' => 'Usar rutas relativas: yes|no|1|0'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->css_scan(...$this->toOpt($parsed));
    }
}
