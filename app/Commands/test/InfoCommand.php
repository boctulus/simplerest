<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

/**
 * Stress-test: no required args, optional format choice, verbose flag.
 *
 * php com test info
 * php com test info --verbose
 * php com test info --format=json
 */
class InfoCommand extends BaseCommand
{
    public function __construct()
    {
        $this->command     = 'info';
        $this->description = 'Muestra info del entorno (prueba comando sin args requeridos)';
        $this->aliases     = ['env', 'status'];
        $this->examples    = [
            'php com test info',
            'php com test info --verbose',
            'php com test info --format=json',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['format'],
            'flags'    => ['verbose'],
            'options'  => [
                'format'  => ['describe' => 'Formato de salida: text, json', 'default' => 'text'],
                'verbose' => ['describe' => 'Incluir información adicional del entorno'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $format  = $this->opt($parsed, 'format', 'text');
        $verbose = $this->opt($parsed, 'verbose', false);

        $data = [
            'php_version'  => PHP_VERSION,
            'php_sapi'     => PHP_SAPI,
            'os'           => PHP_OS,
            'timestamp'    => date('Y-m-d H:i:s'),
        ];

        if ($verbose) {
            $data['memory_limit']   = ini_get('memory_limit');
            $data['memory_usage']   = round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB';
            $data['extensions']     = count(get_loaded_extensions());
            $data['root_path']      = defined('ROOT_PATH') ? ROOT_PATH : getcwd();
            $data['commands_path']  = defined('COMMANDS_PATH') ? COMMANDS_PATH : 'N/A';
        }

        if ($format === 'json') {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            return;
        }

        echo "\nEntorno:\n";
        echo str_repeat('-', 30) . "\n";
        foreach ($data as $key => $value) {
            printf("  %-20s %s\n", $key . ':', $value);
        }
        echo "\n";
    }
}
