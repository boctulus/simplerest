<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;

class AclMakeCommand extends BaseCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        $this->command     = 'make';
        $this->description = 'Genera el archivo ACL desde config/acl.php';
        $this->aliases     = ['generate', 'gen', 'build'];
        $this->examples    = [
            'php com acl make',
            'php com acl make --force',
            'php com acl make --debug',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => ['force', 'f', 'debug', 'd', 'dd'],
            'options'  => [
                'force' => ['describe' => 'Eliminar roles previos antes de generar'],
                'debug' => ['describe' => 'Mostrar ACL generado'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $debug = $parsed['debug'] ?? $parsed['dd'] ?? $parsed['d'] ?? false;
        $force = $parsed['force'] ?? $parsed['f'] ?? false;

        if (!isset(Config::get()['acl_file'])) {
            echo "✗ ACL filename not defined in config.\n";
            return;
        }

        if (file_exists(Config::get()['acl_file'])) {
            unlink(Config::get()['acl_file']);
        }

        if ($force) {
            dd("Deleting previous roles");
            DB::table('roles')->whereRaw("1=1")->delete();
        }

        try {
            $acl = include CONFIG_PATH . 'acl.php';

            if ($debug) {
                dd((array) $acl, 'ACL generated');
            }

            dd("ACL file was generated. Path: " . SECURITY_PATH);
        } catch (\Exception $e) {
            throw new \Exception("Acl generation fails. Detail: " . $e->getMessage());
        }
    }
}
