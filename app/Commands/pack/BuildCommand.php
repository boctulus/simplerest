<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

class BuildCommand extends BaseCommand
{
    private string $defaultSource = 'D:\\laragon\\www\\simplerest';
    private string $defaultDest   = 'D:\\laragon\\www\\simplerest-pack';

    public function __construct()
    {
        $this->command     = 'build';
        $this->description = 'Empaqueta el framework SimpleRest en un directorio de distribución';
        $this->aliases     = ['run', 'package'];
        $this->examples    = [
            'php com pack build',
            'php com pack build --dry-run',
            'php com pack build --skip-verification',
            'php com pack build --source=D:\\custom\\src --dest=D:\\custom\\dist',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['source', 'dest'],
            'flags'    => ['skip-verification', 'skip-composer-install', 'dry-run'],
            'options'  => [
                'source'                => ['describe' => 'Directorio fuente'],
                'dest'                  => ['describe' => 'Directorio destino'],
                'skip-verification'     => ['describe' => 'Omitir la verificación en destino'],
                'skip-composer-install' => ['describe' => 'Omitir composer install en destino'],
                'dry-run'               => ['describe' => 'Mostrar qué se haría sin ejecutar nada'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $source              = $this->opt($parsed, 'source', $this->defaultSource);
        $dest                = $this->opt($parsed, 'dest',   $this->defaultDest);
        $skipVerification    = $this->opt($parsed, 'skip_verification', false);
        $skipComposerInstall = $this->opt($parsed, 'skip_composer_install', false);
        $dryRun              = $this->opt($parsed, 'dry_run', false);

        require_once __DIR__ . '/../../../scripts/pack_framework.php';

        $packager = new SimpleRestPackager($source, $dest);

        if ($dryRun) {
            $packager->dryRun();
            return;
        }

        echo "Empaquetando SimpleRest framework...\n";
        echo "Fuente:  {$source}\n";
        echo "Destino: {$dest}\n";

        if ($skipVerification)    echo "  (sin verificación)\n";
        if ($skipComposerInstall) echo "  (sin composer install)\n";

        if ($packager->run($skipVerification, $skipComposerInstall)) {
            echo "\n✓ Framework empaquetado exitosamente.\n";
        } else {
            echo "\n✗ Error durante el empaquetado.\n";
        }
    }
}
