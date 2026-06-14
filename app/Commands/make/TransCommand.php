<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeTransCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'trans';
        $this->description = 'Genera archivos de traducción (.pot, .po, .mo)';
        $this->aliases     = ['translations', 'i18n'];
        $this->examples    = [
            'php com make trans',
            'php com make trans --preset=wp',
            'php com make trans --pot --domain=my-domain',
            'php com make trans --po --mo --preset=wp',
            'php com make trans --from=/path/to/locale --to=/path/out --domain=myapp',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['preset', 'domain', 'from', 'to'],
            'flags'    => ['pot', 'po', 'mo'],
            'options'  => [
                'preset' => ['describe' => 'Preset de traducción (ej: wp)'],
                'domain' => ['describe' => 'Text-domain'],
                'from'   => ['describe' => 'Directorio fuente'],
                'to'     => ['describe' => 'Directorio de salida'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $this->trans(...$this->toOpt($parsed));
    }
}
