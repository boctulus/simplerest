<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\Libs\DB;

class StartCommand extends BaseCommand
{
    public function __construct()
    {
        $this->command     = 'start';
        $this->description = 'Activa el log de MySQL con archivo opcional (DB::dbLogStart)';
        $this->examples    = [
            'php com mysql_log start',
            'php com mysql_log start --filename=my_log.sql',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => ['filename'],
            'flags'    => [],
            'options'  => [
                'filename' => ['describe' => 'Nombre del archivo de log'],
            ],
        ];
    }

    public function execute(array $parsed): void
    {
        $filename = $this->opt($parsed, 'filename');
        echo "Activando logs ...\n";
        $filename ? DB::dbLogStart($filename) : DB::dbLogStart();
    }
}
