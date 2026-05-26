<?php

require_once __DIR__ . '/BaseMigrationsCommand.php';

class MigrationsCommand extends BaseMigrationsCommand
{
    public function migrate(...$args)
    {
        $this->doMigrate(...$args);
    }

    public function execute(array $parsed): void
    {
        $this->doMigrate(...$this->toOpt($parsed));
    }
}
