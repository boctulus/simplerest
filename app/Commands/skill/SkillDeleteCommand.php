<?php

require_once __DIR__ . '/BaseSkillCommand.php';

class SkillDeleteCommand extends BaseSkillCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'delete';
        $this->description = 'Elimina un skill por slug de todos los directorios de agentes';
        $this->aliases     = ['rm', 'remove', 'del'];
        $this->examples    = [
            'php com skill delete gpt-architecture-consult',
            'php com skill delete todo-kanban',
            'php com skill delete my-skill --dry-run',
            'php com skill delete my-skill -y',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['dry-run'],
            'flags'    => ['dry-run', 'y'],
            'options'  => [
                'dry-run' => ['describe' => 'Solo mostrar que se eliminaria sin borrar'],
                'y'       => ['describe' => 'Saltar la confirmacion (automatico)'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $slug   = $parsed['_positional'][0] ?? null;
        $dryRun = $this->opt($parsed, 'dry_run', false);
        $yes    = $this->opt($parsed, 'y', false);

        if (!$slug) {
            $this->log('Debes especificar el slug del skill a eliminar', 'error');
            $this->showUsage();
            return;
        }

        $locations = $this->getAllSkillLocations();
        $found     = [];

        foreach ($locations as $location) {
            $skillPath = $location . DIRECTORY_SEPARATOR . $slug;
            if (is_dir($skillPath)) {
                $found[] = $skillPath;
            }
        }

        if (empty($found)) {
            $this->log(" Skill \"{$slug}\" no encontrado en ningun directorio de agentes", 'error');
            return;
        }

        $this->log(" Skill \"{$slug}\" encontrado en " . count($found) . " ubicacion(es):", 'info');
        foreach ($found as $path) {
            echo "  - {$path}" . PHP_EOL;
        }

        if ($dryRun) {
            $this->log('Modo dry-run: no se elimino nada', 'info');
            return;
        }

        echo PHP_EOL;

        if (!$yes) {
            $this->log(' Eliminar el skill de todas las ubicaciones? (y/N): ', 'warning');
            $handle = fopen('php://stdin', 'r');
            $input  = trim(fgets($handle));
            fclose($handle);

            if (mb_strtolower($input) !== 'y') {
                $this->log('Operacion cancelada', 'info');
                return;
            }
        }

        foreach ($found as $path) {
            if ($this->delTree($path)) {
                $this->log("Eliminado: {$path}", 'success');
            } else {
                $this->log("Error al eliminar: {$path}", 'error');
            }
        }
    }

    protected function getAllSkillLocations(): array
    {
        $projectRoot = dirname(__DIR__, 3);
        $homeDir     = getenv('USERPROFILE') ?: getenv('HOME');

        $agentDirs = ['opencode', 'claude', 'agents', 'qwen'];
        $locations = [];

        foreach ($agentDirs as $dir) {
            $path = $projectRoot . DIRECTORY_SEPARATOR . ".{$dir}" . DIRECTORY_SEPARATOR . 'skills';
            if (is_dir($path)) {
                $locations[] = $path;
            }
        }

        $homeDirs = [
            $homeDir . DIRECTORY_SEPARATOR . '.opencode' . DIRECTORY_SEPARATOR . 'skills',
            $homeDir . DIRECTORY_SEPARATOR . '.claude' . DIRECTORY_SEPARATOR . 'skills',
            $homeDir . DIRECTORY_SEPARATOR . '.config' . DIRECTORY_SEPARATOR . 'opencode' . DIRECTORY_SEPARATOR . 'skills',
        ];

        foreach ($homeDirs as $path) {
            if (is_dir($path)) {
                $locations[] = $path;
            }
        }

        return array_unique($locations);
    }

    protected function delTree(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->delTree($path) : unlink($path);
        }

        return rmdir($dir);
    }
}
