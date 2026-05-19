<?php

require_once __DIR__ . '/BaseZippyCommand.php';

class ZippyShowUnmappedCommand extends BaseZippyCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'show-unmapped';
        $this->description = 'Muestra categorías sin mapear';
        $this->aliases     = ['unmapped'];
        $this->examples    = [
            'php com zippy show-unmapped',
            'php com zippy show-unmapped --limit=50',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['limit'],
            'flags'    => [],
            'options'  => [
                'limit' => ['describe' => 'Límite de resultados'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->delegate->show_unmapped(...$this->toOpt($parsed));
    }
}
