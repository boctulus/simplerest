<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Security\Acl as CoreAcl;

class AclMakeCommand extends BaseCommand
{
    public string $group = 'acl';

    public function __construct()
    {
        $this->command     = 'make';
        $this->description = 'Regenera el ACL y reconcilia roles de forma segura con --force';
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
                'force' => ['describe' => 'Reconstruir roles y migrar asignaciones por nombre dentro de una transaccion'],
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

        $acl_file = Config::get()['acl_file'];
        $previous_acl_cache = file_exists($acl_file) ? file_get_contents($acl_file) : null;

        $previous_rebuild      = Config::get('acl_rebuild', false);
        $previous_defer_writes = Config::get('acl_defer_cache_write', false);

        // Rebuild in memory while keeping the last known-good cache available
        // until the complete operation has succeeded.
        Config::set('acl_rebuild', true);
        Config::set('acl_defer_cache_write', true);

        CoreAcl::deferRoleCatalogPersistence($force);

        try {
            $acl = include CONFIG_PATH . 'acl.php';

            if ($force) {
                $acl->reconcileRoleCatalog();
                echo "ACL role catalog reconciled; user assignments were preserved\n";
            }

            $bytes = file_put_contents($acl_file, serialize($acl), LOCK_EX);
            if ($bytes === false || $bytes === 0) {
                throw new \RuntimeException('ACL cache could not be rewritten after ACL generation');
            }

            if ($debug) {
                dd((array) $acl, 'ACL generated');
            }

            echo "ACL file was generated. Path: " . SECURITY_PATH . "\n";
        } catch (\Throwable $e) {
            if ($previous_acl_cache !== null) {
                file_put_contents($acl_file, $previous_acl_cache, LOCK_EX);
            }
            throw new \Exception("Acl generation fails. Detail: " . $e->getMessage(), 0, $e);
        } finally {
            CoreAcl::deferRoleCatalogPersistence(false);
            Config::set('acl_rebuild', $previous_rebuild);
            Config::set('acl_defer_cache_write', $previous_defer_writes);
        }
    }
}
