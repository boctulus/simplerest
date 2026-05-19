<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

abstract class BaseMakeCommand extends BaseCommand
{
    public string $group = 'make';

    protected object $delegate;

    public function __construct()
    {
        require_once COMMANDS_PATH . '_disabled/MakeCommand.php';
        $this->delegate = new MakeCommand();
    }

    protected function toOpt(array $parsed): array
    {
        $opt = [];
        foreach ($parsed as $key => $value) {
            if (str_starts_with($key, '_')) continue;
            if ($value === true) {
                $opt[] = "--{$key}";
            } elseif ($value !== false && $value !== null) {
                $key_dashed = str_replace('_', '-', $key);
                $opt[] = "--{$key_dashed}={$value}";
            }
        }
        return $opt;
    }

    protected function pos(array $parsed, int $index = 0): ?string
    {
        return $parsed['_positional'][$index] ?? null;
    }
}
