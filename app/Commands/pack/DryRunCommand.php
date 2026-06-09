<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

class DryRunCommand extends BaseCommand
{
    private string $defaultSource = 'D:\\laragon\\www\\simplerest';
    private string $defaultDest   = 'D:\\laragon\\www\\simplerest-pack';

    public function __construct()
    {
        $this->command     = 'dry-run';
        $this->description = 'Muestra qué haría pack build sin ejecutar nada';
        $this->examples    = [
            'php com pack dry-run',
            'php com pack dry-run --source=D:\\custom\\src --dest=D:\\custom\\dist',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['source', 'dest'],
            'flags'    => [],
            'options'  => [
                'source' => ['describe' => 'Directorio fuente'],
                'dest'   => ['describe' => 'Directorio destino'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $source = $this->opt($parsed, 'source', $this->defaultSource);
        $dest   = $this->opt($parsed, 'dest',   $this->defaultDest);

        require_once __DIR__ . '/../../../scripts/pack_framework.php';

        $packager = new SimpleRestPackager($source, $dest);
        $packager->dryRun();
    }
}
