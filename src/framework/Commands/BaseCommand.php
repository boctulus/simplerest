<?php

namespace Boctulus\Simplerest\Core\Commands;

abstract class BaseCommand
{
    public string $group       = '';
    public string $command     = '';
    public string $description = '';
    public array  $examples    = [];
    public array  $aliases     = [];

    abstract public function execute(array $parsed): void;

    /**
     * Override to declare required/optional args, flags, and options.
     *
     * Format:
     *   required → list of arg names that must be present
     *   optional → list of optional arg names
     *   flags    → list of boolean flags (default false)
     *   options  → key => ['describe' => ..., 'default' => ..., 'type' => ...]
     */
    public static function config(): array
    {
        return [
            'required' => [],
            'optional' => [],
            'flags'    => [],
            'options'  => [],
        ];
    }

    /**
     * Parse raw CLI args into an associative array.
     *
     * Supported formats:
     *   --key=value      named value
     *   --key:value      named value (alternate separator)
     *   --key            boolean flag (true)
     *   --key="v a l"   quoted value (quotes stripped)
     *   -f               short flag (single letter)
     *   word             positional → stored in _positional[]
     */
    public function parseArgs(array $rawArgs): array
    {
        $result     = [];
        $positional = [];

        foreach ($rawArgs as $arg) {
            if (preg_match('/^--([^=:]+)[=:](.+)$/', $arg, $m)) {
                $key          = str_replace('-', '_', $m[1]);
                $result[$key] = trim($m[2], '"\'');
            } elseif (preg_match('/^--(.+)$/', $arg, $m)) {
                $key          = str_replace('-', '_', $m[1]);
                $result[$key] = true;
            } elseif (preg_match('/^-([a-zA-Z])$/', $arg, $m)) {
                $result[$m[1]] = true;
            } elseif (!str_starts_with($arg, '-')) {
                $positional[] = $arg;
            }
        }

        // Apply defaults from config
        $config = static::config();

        foreach ($config['options'] ?? [] as $key => $opt) {
            $k = str_replace('-', '_', $key);
            if (!isset($result[$k]) && array_key_exists('default', $opt)) {
                $result[$k] = $opt['default'];
            }
        }

        foreach ($config['flags'] ?? [] as $flag) {
            $k = str_replace('-', '_', $flag);
            if (!isset($result[$k])) {
                $result[$k] = false;
            }
        }

        $result['_positional'] = $positional;
        return $result;
    }

    public function validate(array $parsed): bool
    {
        $config = static::config();

        foreach ($config['required'] ?? [] as $arg) {
            $k = str_replace('-', '_', $arg);
            if (!isset($parsed[$k]) || $parsed[$k] === '' || $parsed[$k] === null) {
                echo "✗ Error: El argumento '--{$arg}' es requerido.\n";
                $this->showUsage();
                return false;
            }
        }

        return true;
    }

    public function getHelp(): array
    {
        return [
            'group'       => $this->group,
            'command'     => $this->command,
            'description' => $this->description,
            'examples'    => $this->examples,
            'aliases'     => $this->aliases,
            'config'      => static::config(),
        ];
    }

    /** Get a parsed option with optional default. Handles --kebab-case → snake_case. */
    protected function opt(array $parsed, string $key, mixed $default = null): mixed
    {
        $k = str_replace('-', '_', $key);
        return $parsed[$k] ?? $default;
    }

    protected function showUsage(): void
    {
        if (!empty($this->examples)) {
            echo "\nEjemplos:\n";
            foreach ($this->examples as $ex) {
                echo "  {$ex}\n";
            }
            echo "\n";
        }
    }

    protected function log(string $message, string $type = 'info'): void
    {
        $icons = ['info' => 'ℹ', 'success' => '✓', 'error' => '✗', 'warning' => '⚠'];
        echo ($icons[$type] ?? '') . " {$message}\n";
    }
}
