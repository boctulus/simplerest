<?php

namespace Boctulus\Simplerest\Tests\Command;

use Boctulus\Simplerest\Core\Libs\CommandRegistry;
use PHPUnit\Framework\TestCase;

class CommandRegistryComposerPackageTest extends TestCase
{
    private array $temporaryRoots = [];
    private array $registeredPackages = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryRoots as $root) {
            $this->removeDirectory($root);
        }

        \Composer\InstalledVersions::reload(require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR
            . 'vendor' . DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR . 'installed.php');

        parent::tearDown();
    }

    public function test_discovers_an_explicitly_enabled_composer_package(): void
    {
        $root = $this->newTemporaryRoot();
        $vendorPath = $root . DIRECTORY_SEPARATOR . 'vendor';
        $packageRoot = $vendorPath . DIRECTORY_SEPARATOR . 'acme' . DIRECTORY_SEPARATOR . 'cli-tools';
        $commandsPath = $packageRoot . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'tools';
        $className = $this->writeCommand($commandsPath, 'publish', [], 'composer-package');
        $this->registerComposerPackage('acme/cli-tools', $packageRoot);

        $registry = new CommandRegistry(false, [
            'application_path' => $root . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands',
            'vendor_path' => $vendorPath,
            'packages' => ['acme/cli-tools'],
        ]);
        $registry->loadGroups();

        $this->assertTrue($registry->hasGroup('tools'));
        $command = $this->registeredCommand($registry, 'tools:publish');
        $this->assertInstanceOf($className, $command);

        ob_start();
        $registry->dispatch('tools', ['publish']);
        $output = ob_get_clean();

        $this->assertSame('composer-package', $output);
    }

    public function test_host_command_wins_by_default_and_package_override_is_explicit(): void
    {
        $root = $this->newTemporaryRoot();
        $hostCommands = $root . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'tools';
        $packageRoot = $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'acme' . DIRECTORY_SEPARATOR . 'cli-tools';
        $packageCommands = $packageRoot . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'tools';
        $hostClass = $this->writeCommand($hostCommands, 'sync', [], 'application');
        $this->writeCommand($packageCommands, 'sync', [], 'extension');
        $this->registerComposerPackage('acme/cli-tools', $packageRoot);

        $config = [
            'application_path' => dirname($hostCommands),
            'vendor_path' => $root . DIRECTORY_SEPARATOR . 'vendor',
            'packages' => ['acme/cli-tools'],
        ];

        $registry = new CommandRegistry(false, $config);
        $registry->loadGroups();

        $this->assertInstanceOf($hostClass, $this->registeredCommand($registry, 'tools:sync'));
        $this->assertStringContainsString('tools:sync', implode("\n", $registry->getDiagnostics()));
        $this->assertStringContainsString('application (host)', implode("\n", $registry->getDiagnostics()));
        $this->assertStringContainsString('acme/cli-tools (extension)', implode("\n", $registry->getDiagnostics()));
        ob_start();
        $registry->dispatch('tools', ['sync']);
        $output = ob_get_clean();
        $this->assertSame('application', $output);

        $config['overrides'] = ['tools:sync' => 'acme/cli-tools'];
        $packageRegistry = new CommandRegistry(false, $config);
        $packageRegistry->loadGroups();
        ob_start();
        $packageRegistry->dispatch('tools', ['sync']);
        $output = ob_get_clean();
        $this->assertSame('extension', $output);
    }

    public function test_host_alias_wins_over_package_command_and_package_override_is_explicit(): void
    {
        $root = $this->newTemporaryRoot();
        $hostCommands = $root . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'tools';
        $packageRoot = $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'acme' . DIRECTORY_SEPARATOR . 'cli-tools';
        $packageCommands = $packageRoot . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'tools';
        $this->writeCommand($hostCommands, 'publish', ['deploy'], 'host-alias');
        $this->writeCommand($packageCommands, 'deploy', [], 'package-command');
        $this->registerComposerPackage('acme/cli-tools', $packageRoot);

        $config = [
            'application_path' => dirname($hostCommands),
            'vendor_path' => $root . DIRECTORY_SEPARATOR . 'vendor',
            'packages' => ['acme/cli-tools'],
        ];

        $registry = new CommandRegistry(false, $config);
        $registry->loadGroups();

        ob_start();
        $registry->dispatch('tools', ['deploy']);
        $output = ob_get_clean();
        $this->assertSame('host-alias', $output);
        $this->assertStringContainsString('tools:deploy', implode("\n", $registry->getDiagnostics()));

        $config['overrides'] = ['tools:deploy' => 'acme/cli-tools'];
        $packageRegistry = new CommandRegistry(false, $config);
        $packageRegistry->loadGroups();
        ob_start();
        $packageRegistry->dispatch('tools', ['deploy']);
        $output = ob_get_clean();
        $this->assertSame('package-command', $output);
    }

    public function test_package_alias_cannot_shadow_a_host_command(): void
    {
        $root = $this->newTemporaryRoot();
        $hostCommands = $root . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'tools';
        $packageRoot = $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'acme' . DIRECTORY_SEPARATOR . 'cli-tools';
        $packageCommands = $packageRoot . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'tools';
        $hostClass = $this->writeCommand($hostCommands, 'deploy', [], 'host-command');
        $this->writeCommand($packageCommands, 'publish', ['deploy'], 'package-alias');
        $this->registerComposerPackage('acme/cli-tools', $packageRoot);

        $registry = new CommandRegistry(false, [
            'application_path' => dirname($hostCommands),
            'vendor_path' => $root . DIRECTORY_SEPARATOR . 'vendor',
            'packages' => ['acme/cli-tools'],
        ]);
        $registry->loadGroups();

        $this->assertInstanceOf($hostClass, $this->registeredCommand($registry, 'tools:deploy'));
        $this->assertStringContainsString('tools:deploy', implode("\n", $registry->getDiagnostics()));
        ob_start();
        $registry->dispatch('tools', ['deploy']);
        $output = ob_get_clean();
        $this->assertSame('host-command', $output);
    }

    public function test_same_priority_extension_collisions_require_an_explicit_provider(): void
    {
        $root = $this->newTemporaryRoot();
        $firstPackage = $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'acme' . DIRECTORY_SEPARATOR . 'first';
        $secondPackage = $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'acme' . DIRECTORY_SEPARATOR . 'second';
        $this->writeCommand($firstPackage . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'tools', 'sync', [], 'first');
        $this->writeCommand($secondPackage . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'tools', 'sync', [], 'second');
        $this->registerComposerPackage('acme/first', $firstPackage);
        $this->registerComposerPackage('acme/second', $secondPackage);

        $config = [
            'application_path' => $root . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands',
            'vendor_path' => $root . DIRECTORY_SEPARATOR . 'vendor',
            'packages' => ['acme/first', 'acme/second'],
        ];

        try {
            (new CommandRegistry(false, $config))->loadGroups();
            $this->fail('Expected a same-priority provider collision.');
        } catch (\LogicException $exception) {
            $this->assertStringContainsString('tools:sync', $exception->getMessage());
            $this->assertStringContainsString('acme/first (extension)', $exception->getMessage());
            $this->assertStringContainsString('acme/second (extension)', $exception->getMessage());
        }

        $config['overrides'] = ['tools:sync' => 'acme/second'];
        $registry = new CommandRegistry(false, $config);
        $registry->loadGroups();
        ob_start();
        $registry->dispatch('tools', ['sync']);
        $output = ob_get_clean();
        $this->assertSame('second', $output);
    }

    public function test_extension_wins_over_framework_default_and_framework_override_is_explicit(): void
    {
        $root = $this->newTemporaryRoot();
        $extensionPath = $root . DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR . 'extension-commands';
        $frameworkPath = $root . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'commands';
        $this->writeCommand($extensionPath . DIRECTORY_SEPARATOR . 'tools', 'sync', [], 'extension');
        $this->writeCommand($frameworkPath . DIRECTORY_SEPARATOR . 'tools', 'sync', [], 'framework');

        $config = [
            'application_path' => $root . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands',
            'paths' => [$extensionPath],
            'framework_paths' => [$frameworkPath],
        ];
        $registry = new CommandRegistry(false, $config);
        $registry->loadGroups();
        $this->assertStringContainsString('framework (framework)', implode("\n", $registry->getDiagnostics()));
        ob_start();
        $registry->dispatch('tools', ['sync']);
        $output = ob_get_clean();
        $this->assertSame('extension', $output);

        $config['overrides'] = ['tools:sync' => 'framework'];
        $frameworkRegistry = new CommandRegistry(false, $config);
        $frameworkRegistry->loadGroups();
        ob_start();
        $frameworkRegistry->dispatch('tools', ['sync']);
        $output = ob_get_clean();
        $this->assertSame('framework', $output);
    }

    public function test_namespaced_package_command_can_share_global_short_name_with_host(): void
    {
        $root = $this->newTemporaryRoot();
        $hostCommands = $root . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'tools';
        $packageRoot = $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'acme' . DIRECTORY_SEPARATOR . 'cli-tools';
        $packageCommands = $packageRoot . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'tools';
        $hostClass = $this->writeCommand($hostCommands, 'sync', [], 'application', 'SharedCommand');
        $packageClass = $this->writeCommand(
            $packageCommands,
            'sync',
            [],
            'extension',
            'SharedCommand',
            'Acme\\CliTools\\Commands\\tools'
        );
        $this->registerComposerPackage('acme/cli-tools', $packageRoot);

        $config = [
            'application_path' => dirname($hostCommands),
            'vendor_path' => $root . DIRECTORY_SEPARATOR . 'vendor',
            'packages' => ['acme/cli-tools'],
        ];
        $registry = new CommandRegistry(false, $config);
        $registry->loadGroups();
        $this->assertInstanceOf($hostClass, $this->registeredCommand($registry, 'tools:sync'));

        $config['overrides'] = ['tools:sync' => 'acme/cli-tools'];
        $registry = new CommandRegistry(false, $config);
        $registry->loadGroups();
        $this->assertInstanceOf($packageClass, $this->registeredCommand($registry, 'tools:sync'));
    }

    public function test_missing_optional_command_package_does_not_break_host_startup(): void
    {
        $root = $this->newTemporaryRoot();
        $staleCommands = $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'acme' . DIRECTORY_SEPARATOR
            . 'not-installed' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . 'tools';
        $this->writeCommand($staleCommands, 'stale', [], 'must-not-load');
        $registry = new CommandRegistry(false, [
            'application_path' => $root . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands',
            'vendor_path' => $root . DIRECTORY_SEPARATOR . 'vendor',
            'packages' => ['acme/not-installed'],
        ]);

        $registry->loadGroups();

        $this->assertFalse($registry->hasGroup('tools'));
    }

    private function registeredCommand(CommandRegistry $registry, string $key): object
    {
        $property = new \ReflectionProperty(CommandRegistry::class, 'commands');
        $commands = $property->getValue($registry);

        $this->assertArrayHasKey($key, $commands);
        return $commands[$key];
    }

    private function registerComposerPackage(string $packageName, string $installPath): void
    {
        $this->registeredPackages[$packageName] = [
            'pretty_version' => '1.0.0',
            'version' => '1.0.0.0',
            'reference' => null,
            'type' => 'library',
            'install_path' => $installPath,
            'aliases' => [],
            'dev_requirement' => false,
        ];

        $installed = require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR
            . 'vendor' . DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR . 'installed.php';
        foreach ($this->registeredPackages as $registeredName => $packageData) {
            $installed['versions'][$registeredName] = $packageData;
        }
        \Composer\InstalledVersions::reload($installed);
    }

    private function writeCommand(
        string $groupPath,
        string $commandName,
        array $aliases,
        string $output,
        ?string $shortClassName = null,
        ?string $namespace = null
    ): string
    {
        if (!is_dir($groupPath) && !mkdir($groupPath, 0777, true) && !is_dir($groupPath)) {
            throw new \RuntimeException("Could not create command fixture path '{$groupPath}'.");
        }

        $className = $shortClassName ?? ('RegistryComposerFixture' . bin2hex(random_bytes(6)) . 'Command');
        $qualifiedClassName = $namespace === null ? $className : $namespace . '\\' . $className;
        $aliasesCode = var_export($aliases, true);
        $commandCode = var_export($commandName, true);
        $outputCode = var_export($output, true);
        $namespaceCode = $namespace === null ? '' : "namespace {$namespace};\n\n";
        $source = "<?php\n"
            . $namespaceCode
            . "use Boctulus\\Simplerest\\Core\\Commands\\BaseCommand;\n\n"
            . "class {$className} extends BaseCommand\n{\n"
            . "    public function __construct()\n    {\n"
            . "        \$this->command = {$commandCode};\n"
            . "        \$this->aliases = {$aliasesCode};\n"
            . "    }\n\n"
            . "    public function execute(array \$parsed): void\n    {\n"
            . "        echo {$outputCode};\n"
            . "    }\n"
            . "}\n";

        $path = $groupPath . DIRECTORY_SEPARATOR . $className . '.php';
        if (file_put_contents($path, $source) === false) {
            throw new \RuntimeException("Could not write command fixture '{$path}'.");
        }

        return $qualifiedClassName;
    }

    private function newTemporaryRoot(): string
    {
        $root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'simplerest-cli-' . bin2hex(random_bytes(8));
        if (!mkdir($root, 0777, true) && !is_dir($root)) {
            throw new \RuntimeException("Could not create temporary test directory '{$root}'.");
        }

        $this->temporaryRoots[] = $root;
        return $root;
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($directory);
    }
}
