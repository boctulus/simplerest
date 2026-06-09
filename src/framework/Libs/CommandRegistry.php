<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Commands\BaseCommand;

/**
 * Auto-discovers and dispatches commands organized in group folders.
 *
 * Convention:
 *   app/Commands/{group}/{Name}Command.php  → php com {group} {name}
 *
 * Directories starting with '_' or '.' are skipped (e.g., _disabled).
 * Files starting with 'Base' or 'Abstract' are skipped (base classes).
 */
class CommandRegistry
{
    private array $commands     = [];  // 'group:command' => BaseCommand instance
    private array $groups       = [];  // 'group'         => ['cmd1', 'cmd2', ...]
    private array $aliases      = [];  // 'group:alias'   => ['group' => ..., 'command' => ...]
    private array $crossAliases = [];  // 'group:cmd'     => ['group' => ..., 'command' => ...]
    private bool  $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    public static function init(bool $debug = false): self
    {
        $registry = new self($debug);
        $registry->loadGroups();
        return $registry;
    }

    public function loadGroups(): void
    {
        $config = $this->loadCommandsConfig();

        foreach ($this->buildSearchPaths($config) as $basePath) {
            if (!is_dir($basePath)) continue;

            foreach (scandir($basePath) as $entry) {
                if ($entry === '.' || $entry === '..') continue;
                if (str_starts_with($entry, '_') || str_starts_with($entry, '.')) continue;

                $groupPath = $basePath . DIRECTORY_SEPARATOR . $entry;
                if (!is_dir($groupPath)) continue;

                $this->loadCommandsFromDirectory($groupPath, $entry);
            }
        }

        $this->loadCrossAliases($config);

        if ($this->debug) {
            $total  = count($this->commands);
            $groups = count($this->groups);
            echo "ℹ Auto-discovery: {$total} command(s) in {$groups} group(s)\n";
        }
    }

    private function loadCommandsConfig(): array
    {
        $configFile = defined('ROOT_PATH') ? ROOT_PATH . 'config/commands.php' : null;
        if ($configFile && file_exists($configFile)) {
            return require $configFile;
        }
        return [];
    }

    private function loadCrossAliases(array $config): void
    {
        foreach ($config['cross_aliases'] ?? [] as $source => $target) {
            [$sg, $sc] = array_pad(explode(' ', trim($source), 2), 2, '');
            [$tg, $tc] = array_pad(explode(' ', trim($target), 2), 2, '');
            if (!$sg || !$sc || !$tg || !$tc) continue;
            $this->crossAliases["{$sg}:{$sc}"] = ['group' => $tg, 'command' => $tc];
        }
    }

    private function buildSearchPaths(array $config): array
    {
        $paths = [];

        if (defined('COMMANDS_PATH')) {
            $paths[] = COMMANDS_PATH;
        }

        foreach ($config['paths'] ?? [] as $path) {
            $resolved = $this->resolvePath($path);
            if ($resolved && is_dir($resolved)) {
                $paths[] = $resolved;
            }
        }

        foreach ($config['packages'] ?? [] as $packageId) {
            [$vendor, $package] = array_pad(explode('/', $packageId, 2), 2, '');
            if (!$vendor || !$package) continue;

            $packageCommandsPath = defined('PACKAGES_PATH')
                ? PACKAGES_PATH . $vendor . DIRECTORY_SEPARATOR . $package . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Commands'
                : null;

            if ($packageCommandsPath && is_dir($packageCommandsPath)) {
                $paths[] = $packageCommandsPath;
            }
        }

        return $paths;
    }

    private function resolvePath(string $path): ?string
    {
        if (defined('ROOT_PATH') && !preg_match('/^([A-Za-z]:[\\/]|[\\/])/', $path)) {
            return rtrim(ROOT_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
        }
        return $path;
    }

    private function loadCommandsFromDirectory(string $dirPath, string $groupName): void
    {
        $files = glob($dirPath . DIRECTORY_SEPARATOR . '*Command.php') ?: [];

        foreach ($files as $file) {
            $basename = basename($file, '.php');
            if (str_starts_with($basename, 'Base') || str_starts_with($basename, 'Abstract')) continue;
            if (str_ends_with($basename, 'BaseCommand')) continue;

            $this->loadCommandFromFile($file, $groupName);
        }
    }

    private function loadCommandFromFile(string $filePath, string $groupName): void
    {
        $before = get_declared_classes();

        try {
            require_once $filePath;
        } catch (\Throwable $e) {
            fwrite(STDERR, "Error loading {$filePath}: " . $e->getMessage() . "\n");
            return;
        }

        $after      = get_declared_classes();
        $newClasses = array_diff($after, $before);

        $commandClass = null;
        foreach ($newClasses as $class) {
            if (str_ends_with($class, 'Command')) {
                $commandClass = $class;
                break;
            }
        }

        // Fallback: match by filename
        if (!$commandClass) {
            $basename = basename($filePath, '.php');
            if (class_exists($basename)) {
                $commandClass = $basename;
            }
        }

        if (!$commandClass || !class_exists($commandClass)) return;

        try {
            $instance = new $commandClass();
        } catch (\Throwable $e) {
            fwrite(STDERR, "Error instantiating {$commandClass}: " . $e->getMessage() . "\n");
            return;
        }

        if (!($instance instanceof BaseCommand)) return;
        if (empty($instance->command)) return;

        $instance->group = $groupName;

        $key = "{$groupName}:{$instance->command}";
        $this->commands[$key] = $instance;

        $this->groups[$groupName]   ??= [];
        $this->groups[$groupName][]   = $instance->command;

        foreach ($instance->aliases as $alias) {
            $this->aliases["{$groupName}:{$alias}"] = [
                'group'   => $groupName,
                'command' => $instance->command,
            ];
        }

        if ($this->debug) {
            echo "  ✓ {$groupName} {$instance->command}\n";
        }
    }

    public function hasGroup(string $group): bool
    {
        return isset($this->groups[$group]);
    }

    public function dispatch(string $group, array $args): void
    {
        $isHelp = in_array('--help', $args) || in_array('-h', $args);

        // No subcommand or bare --help → show group help
        if (count($args) === 0) {
            $this->showGroupHelp($group);
            return;
        }

        // 'help' as first arg → verbose group help
        if ($args[0] === 'help') {
            $this->showGroupHelp($group, verbose: true);
            return;
        }

        // Extract first non-option arg as command name
        $commandName = null;
        $commandArgs = [];

        foreach ($args as $arg) {
            if ($commandName === null && !str_starts_with($arg, '-')) {
                $commandName = $arg;
            } else {
                $commandArgs[] = $arg;
            }
        }

        if (!$commandName) {
            $this->showGroupHelp($group);
            return;
        }

        // --help after command name → show command help
        if ($isHelp) {
            $this->showCommandHelp($group, $commandName);
            return;
        }

        $command = $this->resolveCommand($group, $commandName);

        if (!$command) {
            echo "✗ Comando '{$commandName}' no encontrado en grupo '{$group}'.\n\n";
            $this->showGroupHelp($group);
            exit(1);
        }

        $parsed = $command->parseArgs($commandArgs);

        if (!$command->validate($parsed)) {
            exit(1);
        }

        $command->execute($parsed);
    }

    private function resolveCommand(string $group, string $commandName): ?BaseCommand
    {
        $key = "{$group}:{$commandName}";

        // Cross-group aliases take precedence
        if (isset($this->crossAliases[$key])) {
            $info = $this->crossAliases[$key];
            return $this->commands["{$info['group']}:{$info['command']}"] ?? null;
        }

        // Within-group aliases
        if (isset($this->aliases[$key])) {
            $info = $this->aliases[$key];
            return $this->commands["{$info['group']}:{$info['command']}"] ?? null;
        }

        return $this->commands[$key] ?? null;
    }

    // -------------------------------------------------------------------------
    // Help display
    // -------------------------------------------------------------------------

    public function showHelp(?string $group = null, ?string $command = null): void
    {
        if (!$group) {
            $this->showAllGroups();
        } elseif (!$command) {
            $this->showGroupHelp($group);
        } else {
            $this->showCommandHelp($group, $command);
        }
    }

    public function showAllGroups(): void
    {
        $title  = '📋 Sistema de Comandos CLI';
        $width  = 63;
        $inner  = $width - 2;
        $pad    = $inner - mb_strlen($title);
        $padL   = (int) floor($pad / 2);
        $padR   = $pad - $padL;

        echo "\n";
        echo '┌' . str_repeat('─', $inner) . "┐\n";
        echo '│' . str_repeat(' ', $padL) . $title . str_repeat(' ', $padR) . "│\n";
        echo '└' . str_repeat('─', $inner) . "┘\n\n";

        echo "💡 Uso: php com <grupo> <comando> [argumentos]\n\n";

        if (empty($this->groups)) {
            echo "⚠️  No se encontraron grupos de comandos.\n\n";
            return;
        }

        echo "🗂️  Grupos de comandos disponibles:\n\n";

        foreach ($this->groups as $groupName => $commands) {
            $crossCount  = count(array_filter(array_keys($this->crossAliases), fn($k) => str_starts_with($k, "{$groupName}:")));
            $count       = count($commands) + $crossCount;
            $capitalized = ucfirst($groupName);
            $plural      = $count === 1 ? 'comando' : 'comandos';
            printf("  🔹 %-18s (%d %s)\n", $capitalized, $count, $plural);
        }

        echo "\n💡 Ejemplos de uso:\n";
        echo "   • php com <grupo> <comando>\n";
        echo "   • php com <grupo> <comando> --help\n";
        echo "   • php com help <grupo>    (ver comandos del grupo)\n";
        echo "\n";
    }

    public function showGroupHelp(string $group, bool $verbose = false): void
    {
        if (!isset($this->groups[$group])) {
            echo "✗ Grupo '{$group}' no encontrado.\n";
            $this->showAllGroups();
            return;
        }

        echo "\nComandos del grupo '{$group}':\n\n";

        foreach ($this->groups[$group] as $commandName) {
            $command = $this->commands["{$group}:{$commandName}"];
            $help    = $command->getHelp();
            $config  = $help['config'];

            printf("  %-28s %s\n", $commandName, $command->description);

            if (!empty($help['aliases'])) {
                printf("  %-28s Aliases: %s\n", '', implode(', ', $help['aliases']));
            }

            if ($verbose) {
                if (!empty($config['required'])) {
                    printf("  %-28s Requeridos: --%s\n", '', implode(', --', $config['required']));
                }
                foreach ($config['options'] ?? [] as $opt => $def) {
                    $default = isset($def['default']) ? " (default: {$def['default']})" : '';
                    printf("  %-28s   --%-20s %s%s\n", '', $opt, $def['describe'] ?? '', $default);
                }
                if (!empty($help['examples'])) {
                    printf("  %-28s Ejemplos:\n", '');
                    foreach ($help['examples'] as $ex) {
                        printf("  %-28s   %s\n", '', $ex);
                    }
                }
                echo "\n";
            }
        }

        // Show cross-group aliases for this group
        $groupCrossAliases = array_filter(
            $this->crossAliases,
            fn($k) => str_starts_with($k, "{$group}:"),
            ARRAY_FILTER_USE_KEY
        );

        if (!empty($groupCrossAliases)) {
            foreach ($groupCrossAliases as $key => $target) {
                $aliasCmd      = substr($key, strlen($group) + 1);
                $targetCmd     = $this->commands["{$target['group']}:{$target['command']}"] ?? null;
                $desc          = $targetCmd ? $targetCmd->description : '';
                $targetLabel   = "→ {$target['group']} {$target['command']}";
                printf("  %-28s %s\n", $aliasCmd, $desc);
                printf("  %-28s Alias de: %s\n", '', $targetLabel);
            }
            echo "\n";
        }

        echo "Usa 'php com {$group} <comando> --help' para detalles de un comando.\n";
        echo "Usa 'php com {$group} help' para ayuda completa del grupo.\n\n";
    }

    public function showCommandHelp(string $group, string $commandName): void
    {
        $command = $this->resolveCommand($group, $commandName);

        if (!$command) {
            echo "✗ Comando '{$commandName}' no encontrado en grupo '{$group}'.\n";
            $this->showGroupHelp($group);
            return;
        }

        $help   = $command->getHelp();
        $config = $help['config'];

        echo "\n";
        echo "Comando: php com {$group} {$commandName}\n";
        echo str_repeat('-', 50) . "\n";
        echo "Descripción: {$command->description}\n";

        if (!empty($help['aliases'])) {
            echo "Aliases: " . implode(', ', $help['aliases']) . "\n";
        }

        if (!empty($config['required'])) {
            echo "\nArgumentos requeridos:\n";
            foreach ($config['required'] as $arg) {
                $def  = $config['options'][$arg] ?? [];
                $desc = $def['describe'] ?? '';
                echo "  --{$arg}  {$desc}\n";
            }
        }

        if (!empty($config['optional'])) {
            echo "\nArgumentos opcionales:\n";
            foreach ($config['optional'] as $arg) {
                $def     = $config['options'][$arg] ?? [];
                $desc    = $def['describe'] ?? '';
                $default = isset($def['default']) ? " (default: {$def['default']})" : '';
                echo "  --{$arg}  {$desc}{$default}\n";
            }
        }

        if (!empty($config['flags'])) {
            echo "\nFlags:\n";
            foreach ($config['flags'] as $flag) {
                $def  = $config['options'][$flag] ?? [];
                $desc = $def['describe'] ?? '';
                echo "  --{$flag}  {$desc}\n";
            }
        }

        if (!empty($help['examples'])) {
            echo "\nEjemplos:\n";
            foreach ($help['examples'] as $ex) {
                echo "  {$ex}\n";
            }
        }

        echo "\n";
    }
}
