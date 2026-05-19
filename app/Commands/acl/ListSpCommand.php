<?php

use Boctulus\Simplerest\Core\Libs\DB;

require_once __DIR__ . '/BaseAclCommand.php';

class ListSpCommand extends BaseAclCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        parent::__construct();
        $this->command     = 'list-sp';
        $this->description = 'Lista todos los special permissions (capabilities) disponibles';
        $this->aliases     = ['ls-sp'];
        $this->examples    = [
            'php com acl list-sp',
            'php com acl list-sp --format=json',
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
        $perms  = $this->withDb(fn() => DB::table('sp_permissions')->get());

        if (empty($perms)) {
            echo "No se encontraron special permissions.\n";
            return;
        }

        if ($format === 'json') {
            echo json_encode($perms, JSON_PRETTY_PRINT) . "\n";
            return;
        }

        $this->printTable($perms, ['id', 'name']);
    }
}
