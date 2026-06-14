<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeDtoCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'dto';
        $this->description = 'Genera un DTO (Data Transfer Object)';
        $this->aliases     = [];
        $this->examples    = [
            'php com make dto MyDto',
            'php com make dto MyDto --dir=app/DTOs',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['dir'],
            'flags'    => ['force', 'f'],
            'options'  => [
                'dir' => ['describe' => 'Directorio de salida'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed);
        if (!$name) { echo "✗ Se requiere el nombre del DTO.\n"; return; }
        $this->dto($name, ...$this->toOpt($parsed));
    }
}
