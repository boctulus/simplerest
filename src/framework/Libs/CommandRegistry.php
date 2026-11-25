<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Commands\BaseCommand;

/**
 * Auto-discovers commands from group folders and flat command roots.
 *
 * Convention:
 *   app/Commands/{group}/{Name}Command.php → group comes from the directory
 *   flat package src/Commands/{Name}Command.php → group comes from BaseCommand::$group
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
    private array $commandCandidates = [];
    private array $aliasCandidates   = [];
    private array $diagnostics       = [];
    private ?array $composerCommandClassMap = null;
    private ?array $providedConfig    = null;
    private bool  $debug;

    public function __construct(bool $debug = false, ?array $config = null)
    {
        $this->debug = $debug;
        $this->providedConfig = $config;
    }

    public static function init(bool $debug = false, ?array $config = null): self
    {
        $registry = new self($debug, $config);
        $registry->loadGroups();
        return $registry;
    }

    public function loadGroups(): void
    {
        $config = $this->loadCommandsConfig();
        $this->commands = [];
        $this->groups = [];
        $this->aliases = [];
        $this->crossAliases = [];
        $this->commandCandidates = [];
        $this->aliasCandidates = [];
        $this->diagnostics = [];

        foreach ($this->buildSearchSources($config) as $source) {
            $basePath = $source['path'];
            if (!is_dir($basePath)) continue;

            if (in_array($source['id'], $config['packages'] ?? [], true)) {
                $this->loadCommandsFromDirectory($basePath, null, $source['id'], $source['tier']);
            }

            foreach (scandir($basePath) as $entry) {
                if ($entry === '.' || $entry === '..') continue;
                if (str_starts_with($entry, '_') || str_starts_with($entry, '.')) continue;

                $groupPath = $basePath . DIRECTORY_SEPARATOR . $entry;
                if (!is_dir($groupPath)) continue;

                $this->loadCommandsFromDirectory($groupPath, $entry, $source['id'], $source['tier']);
            }
        }

        $this->resolveCommandCandidates($config);
        $this->reportDiagnostics();
        $this->loadCrossAliases($config);

        if ($this->debug) {
            $total  = count($this->commands);
            $groups = count($this->groups);
            echo "ℹ Auto-discovery: {$total} command(s) in {$groups} group(s)\n";
        }
    }

    private function loadCommandsConfig(): array
    {
        if ($this->providedConfig !== null) {
            return $this->providedConfig;
        }

        $configFile = defined('ROOT_PATH') ? ROOT_PATH . 'config/commands.php' : null;
        if ($configFile && file_exists($configFile)) {
            $config = require $configFile;
            if (!is_array($config)) {
                throw new \UnexpectedValueException("Command configuration must return an array: {$configFile}");
            }
            return $config;
        }
        return [];
    }

    private function loadCrossAliases(array $config): void
    {
        foreach ($config['cross_aliases'] ?? [] as $source => $target) {
            [$sg, $sc] = array_pad(explode(' ', trim($source), 2), 2, '');
            [$tg, $tc] = array_pad(explode(' ', trim($target), 2), 2, '');
            if (!$sg || !$sc || !$tg || !$tc) {
                throw new \InvalidArgumentException("Invalid cross-group command alias '{$source}' => '{$target}'");
            }

            $key = "{$sg}:{$sc}";
            if (isset($this->commands[$key]) || isset($this->aliases[$key])) {
                throw new \LogicException(
                    "Cross-group alias collision for '{$key}'. Remove the conflicting command or alias before registering the cross-group alias."
                );
            }

            if (!isset($this->commands["{$tg}:{$tc}"]) && !isset($this->aliases["{$tg}:{$tc}"])) {
                throw new \LogicException("Cross-group alias '{$key}' points to missing command '{$tg}:{$tc}'");
            }

            $this->crossAliases[$key] = ['group' => $tg, 'command' => $tc];
        }
    }

    private function buildSearchSources(array $config): array
    {
        $sources = [];

        $hostPath = $config['application_path'] ?? (defined('COMMANDS_PATH') ? COMMANDS_PATH : null);
        if (is_string($hostPath) && $hostPath !== '') {
            $resolved = $this->resolvePath($hostPath);
            if ($resolved) {
                $sources[] = ['path' => $resolved, 'id' => 'application', 'tier' => 'host'];
            }
        }

        foreach ($config['paths'] ?? [] as $path) {
            $resolved = $this->resolvePath($path);
            if ($resolved) {
                $sources[] = ['path' => $resolved, 'id' => 'path:' . $resolved, 'tier' => 'extension'];
            }
        }

        foreach ($config['packages'] ?? [] as $packageId) {
            if (!is_string($packageId) || !preg_match('/^[A-Za-z0-9_.-]+\/[A-Za-z0-9_.-]+$/', $packageId)) {
                throw new \InvalidArgumentException('Enabled command package must use a valid vendor/package name.');
            }

            $packageCommandsPath = $this->resolvePackageCommandsPath($packageId, $config);
            if ($packageCommandsPath !== null) {
                $sources[] = ['path' => $packageCommandsPath, 'id' => $packageId, 'tier' => 'extension'];
            }
        }

        foreach ($config['framework_paths'] ?? [] as $path) {
            $resolved = $this->resolvePath($path);
            if ($resolved) {
                $sources[] = ['path' => $resolved, 'id' => 'framework', 'tier' => 'framework'];
            }
        }

        return $sources;
    }

    private function resolvePackageCommandsPath(string $packageId, array $config): ?string
    {
        $installPath = null;
        $hasInstalledVersions = class_exists('Composer\\InstalledVersions')
            && method_exists('Composer\\InstalledVersions', 'getInstallPath');
        if ($hasInstalledVersions) {
            try {
                $installPath = \Composer\InstalledVersions::getInstallPath($packageId);
            } catch (\OutOfBoundsException) {
                $installPath = null;
            }
        }

        [$vendor, $package] = explode('/', $packageId, 2);
        $vendorRoot = $config['vendor_path']
            ?? (defined('VENDOR_PATH') ? VENDOR_PATH : (defined('ROOT_PATH') ? ROOT_PATH . 'vendor' . DIRECTORY_SEPARATOR : null));

        // Composer is authoritative when its installed-package registry is available.
        // In particular, do not expose a stale path-repository symlink after --no-dev.
        if ($installPath === null && !$hasInstalledVersions && is_string($vendorRoot)) {
            $installPath = rtrim($vendorRoot, '/\\') . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . $package;
        }

        $candidateRoots = [];
        if (is_string($installPath)) {
            $candidateRoots[] = $installPath;
        }

        $localRoot = $config['packages_path']
            ?? (defined('ROOT_PATH') ? ROOT_PATH . 'packages' . DIRECTORY_SEPARATOR : null);
        if (is_string($localRoot)) {
            $candidateRoots[] = rtrim($localRoot, '/\\') . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . $package;
        }

        if (defined('PACKAGES_PATH')) {
            $candidateRoots[] = rtrim(PACKAGES_PATH, '/\\') . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . $package;
        }

        foreach ($candidateRoots as $packageRoot) {
            $commandsPath = $packageRoot . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Commands';
            if (is_dir($commandsPath)) {
                return $commandsPath;
            }
        }

        // An explicitly enabled but absent optional package must not break host startup.
        return null;
    }

    private function resolvePath(string $path): ?string
    {
        $hasDriveRoot = strlen($path) >= 3
            && ctype_alpha($path[0])
            && $path[1] === ':'
            && ($path[2] === '\\' || $path[2] === '/');
        if ($hasDriveRoot || str_starts_with($path, '/') || str_starts_with($path, '\\')) {
            return $path;
        }

        if (defined('ROOT_PATH') && !preg_match('/^([A-Za-z]:[\\/]|[\\/])/', $path)) {
            return rtrim(ROOT_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
        }
        return $path;
    }

    private function loadCommandsFromDirectory(string $dirPath, ?string $groupName, string $sourceId, string $tier): void
    {
        $files = glob($dirPath . DIRECTORY_SEPARATOR . '*Command.php') ?: [];

        foreach ($files as $file) {
            $basename = basename($file, '.php');
            if (str_starts_with($basename, 'Base') || str_starts_with($basename, 'Abstract')) continue;
            if (str_ends_with($basename, 'BaseCommand')) continue;

            $this->loadCommandFromFile($file, $groupName, $sourceId, $tier);
        }
    }

    private function loadCommandFromFile(string $filePath, ?string $groupName, string $sourceId, string $tier): void
    {
        $canonicalFilePath = realpath($filePath) ?: $filePath;
        $commandClass = null;

        $mappedClass = $this->composerCommandClassForFile($canonicalFilePath);
        if ($mappedClass !== null && class_exists($mappedClass)) {
            $commandClass = $this->declaredCommandClassForFile($canonicalFilePath, [$mappedClass]);
        }

        if ($commandClass === null) {
            $commandClass = $this->declaredCommandClassForFile($canonicalFilePath);
        }

        if ($commandClass === null) {
            $before = get_declared_classes();

            try {
                require_once $canonicalFilePath;
            } catch (\Throwable $e) {
                if (str_contains($e->getMessage(), 'Cannot declare class')) {
                    throw new \LogicException(
                        "Command class collision while loading provider '{$sourceId}' from '{$filePath}': " . $e->getMessage(),
                        0,
                        $e
                    );
                }

                fwrite(STDERR, "Error loading command from provider '{$sourceId}' at '{$filePath}': " . $e->getMessage() . "\n");
                return;
            }

            $newClasses = array_diff(get_declared_classes(), $before);
            $commandClass = $this->declaredCommandClassForFile($canonicalFilePath, $newClasses);
        }

        if (!$commandClass) return;

        try {
            $instance = new $commandClass();
        } catch (\Throwable $e) {
            fwrite(STDERR, "Error instantiating command '{$commandClass}' from provider '{$sourceId}': " . $e->getMessage() . "\n");
            return;
        }

        if (!($instance instanceof BaseCommand)) return;
        if (empty($instance->command)) return;

        if ($groupName !== null) {
            $instance->group = $groupName;
        }

        $commandGroup = $groupName ?? $instance->group;
        if ($commandGroup === '') return;

        $key = "{$commandGroup}:{$instance->command}";
        $this->commandCandidates[$key][] = [
            'instance' => $instance,
            'source' => $sourceId,
            'tier' => $tier,
        ];

        foreach ($instance->aliases as $alias) {
            if (!is_string($alias) || trim($alias) === '') {
                throw new \InvalidArgumentException("Command '{$key}' declares an empty or invalid alias.");
            }

            $aliasKey = "{$commandGroup}:{$alias}";
            $this->aliasCandidates[$aliasKey][] = [
                'group' => $commandGroup,
                'command' => $instance->command,
                'owner_key' => $key,
                'source' => $sourceId,
                'tier' => $tier,
            ];
        }

        if ($this->debug) {
            echo "  ✓ {$commandGroup} {$instance->command}\n";
        }
    }

    private function declaredCommandClassForFile(string $filePath, ?array $classes = null): ?string
    {
        $classes ??= get_declared_classes();
        $canonicalFilePath = realpath($filePath) ?: $filePath;

        foreach ($classes as $class) {
            if (!str_ends_with($class, 'Command') || !is_subclass_of($class, BaseCommand::class)) {
                continue;
            }

            $declaredFile = (new \ReflectionClass($class))->getFileName();
            if ($declaredFile && (realpath($declaredFile) ?: $declaredFile) === $canonicalFilePath) {
                return $class;
            }
        }

        return null;
    }

    private function composerCommandClassForFile(string $filePath): ?string
    {
        if ($this->composerCommandClassMap === null) {
            $this->composerCommandClassMap = [];

            if (class_exists('Composer\\Autoload\\ClassLoader')) {
                foreach (\Composer\Autoload\ClassLoader::getRegisteredLoaders() as $loader) {
                    foreach ($loader->getClassMap() as $class => $mappedPath) {
                        if (!str_ends_with($class, 'Command')) {
                            continue;
                        }

                        $canonicalMappedPath = realpath($mappedPath);
                        if ($canonicalMappedPath !== false) {
                            $this->composerCommandClassMap[$canonicalMappedPath] = $class;
                        }
                    }
                }
            }
        }

        $canonicalFilePath = realpath($filePath) ?: $filePath;
        return $this->composerCommandClassMap[$canonicalFilePath] ?? null;
    }

    private function resolveCommandCandidates(array $config): void
    {
        $overrides = $config['overrides'] ?? [];
        if (!is_array($overrides)) {
            throw new \InvalidArgumentException("Command 'overrides' configuration must be an array.");
        }

        $selected = [];
        $keys = array_unique(array_merge(array_keys($this->commandCandidates), array_keys($this->aliasCandidates)));
        sort($keys);

        foreach ($keys as $key) {
            $candidates = [];
            foreach ($this->commandCandidates[$key] ?? [] as $candidate) {
                $candidate['type'] = 'command';
                $candidates[] = $candidate;
            }
            foreach ($this->aliasCandidates[$key] ?? [] as $candidate) {
                $candidate['type'] = 'alias';
                $candidates[] = $candidate;
            }

            $selected[$key] = $this->selectCandidate($key, $candidates, $overrides[$key] ?? null);
        }

        foreach ($selected as $key => $candidate) {
            if ($candidate['type'] === 'alias') {
                $owner = $selected[$candidate['owner_key']] ?? null;
                if (!$owner || $owner['type'] !== 'command' || $owner['source'] !== $candidate['source']) {
                    // Aliases from a command that lost command precedence are not exposed.
                    unset($selected[$key]);
                    continue;
                }
                $this->aliases[$key] = [
                    'group' => $candidate['group'],
                    'command' => $candidate['command'],
                ];
                continue;
            }

            $this->commands[$key] = $candidate['instance'];
            $this->groups[$candidate['instance']->group] ??= [];
            if (!in_array($candidate['instance']->command, $this->groups[$candidate['instance']->group], true)) {
                $this->groups[$candidate['instance']->group][] = $candidate['instance']->command;
            }
        }

        foreach ($this->aliases as $key => $target) {
            $targetKey = "{$target['group']}:{$target['command']}";
            if (!isset($this->commands[$targetKey])) {
                throw new \LogicException("Command alias '{$key}' points to missing or overridden command '{$targetKey}'");
            }
        }
    }

    private function selectCandidate(string $key, array $candidates, ?string $override): array
    {
        if (count($candidates) === 1) {
            if ($override !== null && $override !== $candidates[0]['source']) {
                throw new \LogicException("Command override for '{$key}' names '{$override}', but that provider does not define this name.");
            }
            return $candidates[0];
        }

        if ($override !== null) {
            $matches = array_values(array_filter($candidates, static fn(array $candidate): bool => $candidate['source'] === $override));
            if (count($matches) !== 1) {
                throw new \LogicException("Command override for '{$key}' must identify exactly one provider; '{$override}' matched " . count($matches) . '.');
            }

            return $matches[0];
        }

        $priority = ['host' => 0, 'extension' => 1, 'framework' => 2];
        foreach ($candidates as $candidate) {
            if (!array_key_exists($candidate['tier'], $priority)) {
                throw new \LogicException("Unknown command provider tier '{$candidate['tier']}' for '{$key}'.");
            }
        }

        $bestPriority = min(array_map(static fn(array $candidate): int => $priority[$candidate['tier']], $candidates));
        $winners = array_values(array_filter(
            $candidates,
            static fn(array $candidate): bool => $priority[$candidate['tier']] === $bestPriority
        ));

        if (count($winners) !== 1) {
            $providers = array_map(
                static fn(array $candidate): string => "{$candidate['source']} ({$candidate['tier']})",
                $winners
            );
            throw new \LogicException(
                "Command or alias collision for '{$key}' between providers with the same priority ["
                . implode(', ', $providers) . "]. Set config/commands.php 'overrides' for this name to select one."
            );
        }

        $winner = $winners[0];
        $shadowed = array_values(array_filter($candidates, static fn(array $candidate): bool => $candidate !== $winner));
        $providers = array_map(
            static fn(array $candidate): string => "{$candidate['source']} ({$candidate['tier']})",
            $shadowed
        );
        $this->diagnostics[] = "Resolved '{$key}' to {$winner['source']} ({$winner['tier']}); shadowed "
            . implode(', ', $providers) . ". Configure an override to select another provider.";

        return $winner;
    }

    private function reportDiagnostics(): void
    {
        if ($this->diagnostics === []) {
            return;
        }

        $message = "CommandRegistry resolved command collisions by configured precedence:\n";
        foreach ($this->diagnostics as $diagnostic) {
            $message .= "  - {$diagnostic}\n";
        }

        if (defined('STDERR')) {
            fwrite(STDERR, $message);
            return;
        }

        error_log(rtrim($message));
    }

    public function getDiagnostics(): array
    {
        return $this->diagnostics;
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
