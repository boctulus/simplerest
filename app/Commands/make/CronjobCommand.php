<?php

require_once __DIR__ . '/BaseMakeCommand.php';

class MakeCronjobCommand extends BaseMakeCommand
{
    public function __construct()
    {
        parent::__construct();
        $this->command     = 'cronjob';
        $this->description = 'Genera un CronJob';
        $this->aliases     = ['cron'];
        $this->examples    = [
            'php com make cronjob MyCronJob',
            'php com make cronjob MyCronJob --force',
        ];
    }

    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => ['force', 'f'],
            'options'  => [],
        ];
    }

    public function execute(array $parsed): void
    {
        $name = $this->pos($parsed);
        if (!$name) { echo "✗ Se requiere el nombre del CronJob.\n"; return; }
        $this->delegate->cronjob($name, ...$this->toOpt($parsed));
    }
}
