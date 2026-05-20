<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\Libs\DB;

class DumpCommand extends BaseCommand
{
    public function __construct()
    {
        $this->command     = 'dump';
        $this->description = 'Vuelca el log de MySQL acumulado (DB::dbLogDump)';
        $this->examples    = ['php com mysql_log dump'];
    }

    public function execute(array $parsed): void
    {
        echo "Volcando logs ...\n";
        DB::dbLogDump();
    }
}
