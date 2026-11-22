<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

class RolesListUserRolesCommand extends BaseCommand
{
    public function __construct()
    {
        $this->command     = 'list-user-roles';
        $this->description = 'Lista todos los usuarios con sus roles (alias de acl list-user-roles)';
        $this->aliases     = ['ls-user-roles'];
        $this->examples    = [
            'php com roles list-user-roles',
            'php com roles list-user-roles --role=admin',
            'php com roles list-user-roles --role=null',
            'php com roles list-user-roles --format=json',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['format', 'role'],
            'flags'    => [],
            'options'  => [
                'format' => ['describe' => 'Formato de salida: table (default) | json', 'default' => 'table'],
                'role'   => ['describe' => 'Filtrar por nombre de rol; usar "null" para usuarios sin rol'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        require_once __DIR__ . '/../acl/ListUserRolesCommand.php';
        (new ListUserRolesCommand())->execute($parsed);
    }
}
