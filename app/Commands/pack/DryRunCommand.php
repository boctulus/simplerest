<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

class DryRunCommand extends BaseCommand
{
    private string $defaultSource = 'D:\\laragon\\www\\simplerest';
    private string $defaultDest   = 'D:\\laragon\\www\\simplerest-pack';

    public function __construct()
    {
        $this->command     = 'dry-run';
        $this->description = 'Valida PACK core y muestra las operaciones sin escribir';
        $this->examples    = [
            'php com pack dry-run',
            'php com pack dry-run --source-commit=<sha> --dest=D:\\custom\\dist',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['source', 'dest', 'source-commit', 'profile'],
            'flags'    => [],
            'options'  => [
                'source'          => ['describe' => 'Repositorio canónico fuente'],
                'dest'            => ['describe' => 'Directorio de distribución'],
                'source-commit'   => ['describe' => 'Commit histórico explícito'],
                'profile'         => ['describe' => 'Perfil; únicamente core'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        require_once __DIR__ . '/../../../scripts/pack/build-core.php';

        $arguments = [
            '--dry-run',
            '--profile=' . $this->opt($parsed, 'profile', 'core'),
            '--source=' . $this->opt($parsed, 'source', $this->defaultSource),
            '--dest=' . $this->opt($parsed, 'dest', $this->defaultDest),
        ];
        foreach (['source_commit' => 'source-commit'] as $key => $option) {
            $value = $this->opt($parsed, $key, null);
            if ($value !== null && $value !== '') {
                $arguments[] = '--' . $option . '=' . $value;
            }
        }

        if (SimpleRestCorePackBuilder::main($arguments) !== 0) {
            throw new RuntimeException('PACK core dry-run failed.');
        }
    }
}
