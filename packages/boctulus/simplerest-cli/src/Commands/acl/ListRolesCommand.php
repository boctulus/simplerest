<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseAclCommand.php';

class ListRolesCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'list-roles';
        $this->description = 'Lista todos los roles registrados en DB';
        $this->aliases     = ['ls-roles'];
        $this->examples    = [
            'php com acl list-roles',
            'php com acl list-roles --format=json',
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
        $format = $this->opt($parsed, 'format', 'table');
        $roles  = $this->withDb(fn() => DB::table('roles')->get());

        if (empty($roles)) {
            echo "No se encontraron roles.\n";
            return;
        }

        if ($format === 'json') {
            echo json_encode($roles, JSON_PRETTY_PRINT) . "\n";
            return;
        }

        $this->printTable($roles, ['id', 'name']);
    }
}
