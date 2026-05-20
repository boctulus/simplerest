<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\Libs\DB;

class OnCommand extends BaseCommand
{
    public function __construct()
    {
        $this->command     = 'on';
        $this->description = 'Habilita el log de MySQL (DB::dbLogOn)';
        $this->examples    = ['php com mysql_log on'];
    }

    public function execute(array $parsed): void
    {
        echo "Iniciando logs ...\n";
        DB::dbLogOn();
    }
}
