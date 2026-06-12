<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

class RolesListRolesCommand extends BaseCommand
{
    public function __construct()
    {
        $this->command     = 'list-roles';
        $this->description = 'Lista todos los roles (alias de acl list-roles)';
        $this->aliases     = ['ls-roles'];
        $this->examples    = [
            'php com roles list-roles',
            'php com roles list-roles --format=json',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['format'],
            'flags'    => [],
            'options'  => [
                'format' => ['describe' => 'Formato de salida: table (default) | json', 'default' => 'table'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        require_once __DIR__ . '/../acl/ListRolesCommand.php';
        (new ListRolesCommand())->execute($parsed);
    }
}
