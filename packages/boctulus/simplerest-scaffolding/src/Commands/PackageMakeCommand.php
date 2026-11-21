<?php

namespace Boctulus\Simplerest\CodeTools\Commands\packages;

require_once __DIR__ . '/BaseMakeCommand.php';

class PackageMakeCommand extends \Boctulus\Simplerest\CodeTools\Commands\make\BaseMakeCommand
{
    public string $group = 'packages';

    public function __construct()
    {
        parent::__construct();
        $this->command = 'make';
        $this->description = 'Crea un package con la estructura de SimpleRest';
        $this->examples = ['php com packages make api-client boctulus'];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags' => [],
            'options' => [],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed, 0);
        $author = $this->pos($parsed, 1);
        $destination = $this->pos($parsed, 2);

        if ($name === null || $author === null) {
            echo "Uso: php com packages make <nombre-package> <autor> [destino]\n";
            return;
        }

        $this->package($name, $author, $destination);
    }
}
