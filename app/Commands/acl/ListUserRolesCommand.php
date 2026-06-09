<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseAclCommand.php';

class ListUserRolesCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'list-user-roles';
        $this->description = 'Lista todos los usuarios con sus roles (incluye usuarios sin rol)';
        $this->aliases     = ['ls-user-roles'];
        $this->examples    = [
            'php com acl list-user-roles                   # todos los usuarios',
            'php com acl list-user-roles --role=admin      # solo usuarios con rol "admin"',
            'php com acl list-user-roles --role=null       # solo usuarios sin rol asignado',
            'php com acl list-user-roles --format=json',
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
        $format     = $this->opt($parsed, 'format', 'table');
        $roleFilter = $parsed['role'] ?? null;

        $rows = $this->withDb(function () use ($roleFilter) {
            $q = DB::table($this->usersTable)
                ->leftJoin('user_roles', "{$this->usersTable}.{$this->idField}", '=', 'user_roles.user_id')
                ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
                ->select([
                    "{$this->usersTable}.{$this->idField} as user_id",
                    "{$this->usersTable}.{$this->emailField} as email",
                    'user_roles.role_id',
                    'roles.name as role',
                ]);

            if ($roleFilter === 'null') {
                $q->whereNull('user_roles.role_id');
            } elseif ($roleFilter !== null) {
                $q->where(['roles.name' => $roleFilter]);
            }

            return $q->get() ?: [];
        });

        if (empty($rows)) {
            echo "No se encontraron registros.\n";
            return;
        }

        if ($format === 'json') {
            echo json_encode($rows, JSON_PRETTY_PRINT) . "\n";
            return;
        }

        $this->printTable($rows, ['user_id', 'email', 'role_id', 'role']);
    }
}
