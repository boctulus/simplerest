<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeTestcaseCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'testcase';
        $this->description = 'Genera un TestCase PHPUnit';
        $this->aliases     = ['test', 'phpunit'];
        $this->examples    = [
            'php com make testcase MyFeature',
            'php com make testcase MyFeatureTest --force',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => ['force', 'f'],
            'options'  => [],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed);
        if (!$name) { echo "✗ Se requiere el nombre del test.\n"; return; }
        $this->delegate->testcase($name, ...$this->toOpt($parsed));
    }
}
