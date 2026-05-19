<?php

use Boctulus\Simplerest\Core\Commands\BaseCommand;

abstract class BaseZippyCommand extends BaseCommand
{
    public string $group = 'zippy';

    protected object $delegate;

    public function __construct()
    {
        try {
            require_once __DIR__ . '/../ZippyCommand.php';
            $this->delegate = new \Boctulus\Zippy\Commands\ZippyCommand();
        } catch (\Throwable $e) {
            fwrite(STDERR, "⚠ zippy: " . $e->getMessage() . "\n");
        }
    }

    protected function toOpt(array $parsed): array
    {
        $opt = [];
        foreach ($parsed as $key => $value) {
            if (str_starts_with($key, '_')) continue;
            if ($value === true) {
                $opt[] = "--{$key}";
            } elseif ($value !== false && $value !== null) {
                $opt[] = "--{$key}={$value}";
            }
        }
        return $opt;
    }

    protected function pos(array $parsed, int $index = 0): ?string
    {
        return $parsed['_positional'][$index] ?? null;
    }

    protected function allArgs(array $parsed): array
    {
        return array_merge($parsed['_positional'] ?? [], $this->toOpt($parsed));
    }

    protected function subcommand(array $parsed): ?string
    {
        $sub = $parsed['_positional'][0] ?? null;
        return $sub !== null ? str_replace('-', '_', $sub) : null;
    }

    protected function subOpts(array $parsed): array
    {
        return array_merge(array_slice($parsed['_positional'] ?? [], 1), $this->toOpt($parsed));
    }
}
