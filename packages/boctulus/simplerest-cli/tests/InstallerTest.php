<?php

declare(strict_types=1);

use Boctulus\Simplerest\Cli\Installer;
use PHPUnit\Framework\TestCase;

final class InstallerTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        require_once dirname(__DIR__) . '/src/Installer.php';

        $this->root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'simplerest-cli-installer-' . bin2hex(random_bytes(6));
        mkdir($this->root);
        file_put_contents($this->root . '/composer.json', '{}');
    }

    protected function tearDown(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->root, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($this->root);
    }

    public function testInitCreatesTheBootstrap(): void
    {
        (new Installer())->init($this->root);

        foreach (['com', 'app.php', 'index.php', '.env', '.env.example', 'config/config.php', 'config/constants.php', 'config/commands.php'] as $file) {
            self::assertFileExists($this->root . '/' . $file);
        }
        foreach (['app/Commands', 'database/migrations', 'logs', 'storage/security'] as $directory) {
            self::assertDirectoryExists($this->root . '/' . $directory);
        }
    }

    public function testInitNeverOverwritesExistingFiles(): void
    {
        file_put_contents($this->root . '/app.php', 'custom');
        mkdir($this->root . '/config');
        file_put_contents($this->root . '/config/config.php', 'custom config');

        $report = (new Installer())->init($this->root);

        self::assertSame('custom', file_get_contents($this->root . '/app.php'));
        self::assertSame('custom config', file_get_contents($this->root . '/config/config.php'));
        self::assertContains(['action' => 'SKIP', 'path' => 'app.php'], $report);

        $second = (new Installer())->init($this->root);
        self::assertSame([], array_values(array_filter($second, static fn (array $e): bool => $e['action'] !== 'SKIP')));
    }

    public function testDryRunWritesNothing(): void
    {
        $report = (new Installer())->init($this->root, true);

        self::assertNotEmpty($report);
        self::assertSame(['composer.json'], array_values(array_diff(scandir($this->root), ['.', '..'])));
    }

    public function testInitRequiresAComposerProject(): void
    {
        unlink($this->root . '/composer.json');

        $this->expectException(InvalidArgumentException::class);
        (new Installer())->init($this->root);
    }

    public function testStubsDoNotReferenceMonorepoOnlyCode(): void
    {
        $stubs = dirname(__DIR__) . '/stubs/project';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($stubs, FilesystemIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            // *.example files document optional packages on purpose (e.g. config/acl.php.example).
            if (str_ends_with($file->getFilename(), '.example')) {
                continue;
            }
            $content = file_get_contents($file->getPathname());
            foreach (['FineGrainedACL', 'LLMProviders', 'scripts/pack', 'relmotor', 'Modules\\Security'] as $needle) {
                self::assertStringNotContainsString($needle, $content, $file->getFilename() . " references {$needle}");
            }
        }
    }
}
