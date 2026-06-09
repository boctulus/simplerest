<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;
use Boctulus\Simplerest\Core\Libs\DB;

class OffCommand extends BaseCommand
{
    public function __construct()
    {
        $this->command     = 'off';
        $this->description = 'Deshabilita el log de MySQL (DB::dbLogOff)';
        $this->examples    = ['php com mysql_log off'];
    }

    public function execute(array $parsed): void
    {
        echo "Desactivando logs ...\n";
        DB::dbLogOff();
    }
}
