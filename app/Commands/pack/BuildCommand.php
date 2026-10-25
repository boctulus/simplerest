<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

class BuildCommand extends BaseCommand
{
    private string $defaultSource = 'D:\\laragon\\www\\simplerest';
    private string $defaultDest   = 'D:\\laragon\\www\\simplerest-pack';

    public function __construct()
    {
        $this->command     = 'build';
        $this->description = 'Genera el microcore Composer desde un commit inmutable';
        $this->aliases     = ['run', 'package'];
        $this->examples    = [
            'php com pack build --profile=core',
            'php com pack build --profile=core --dry-run',
            'php com pack build --profile=core --source-commit=<sha> --dest=D:\\custom\\dist',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['source', 'dest', 'source-commit', 'profile', 'package-version'],
            'flags'    => ['dry-run'],
            'options'  => [
                'source'          => ['describe' => 'Repositorio canónico fuente'],
                'dest'            => ['describe' => 'Directorio de distribución'],
                'source-commit'   => ['describe' => 'Commit histórico explícito'],
                'profile'         => ['describe' => 'Perfil; únicamente core'],
                'package-version' => ['describe' => 'Versión prevista para la procedencia'],
                'dry-run'         => ['describe' => 'Validar y mostrar operaciones sin escribir'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        require_once __DIR__ . '/../../../scripts/pack/build-core.php';

        $arguments = [
            '--profile=' . $this->opt($parsed, 'profile', 'core'),
            '--source=' . $this->opt($parsed, 'source', $this->defaultSource),
            '--dest=' . $this->opt($parsed, 'dest', $this->defaultDest),
        ];
        foreach (['source_commit' => 'source-commit', 'package_version' => 'package-version'] as $key => $option) {
            $value = $this->opt($parsed, $key, null);
            if ($value !== null && $value !== '') {
                $arguments[] = '--' . $option . '=' . $value;
            }
        }
        if ($this->opt($parsed, 'dry_run', false)) {
            $arguments[] = '--dry-run';
        }

        if (SimpleRestCorePackBuilder::main($arguments) !== 0) {
            throw new RuntimeException('PACK core build failed.');
        }
    }
}
