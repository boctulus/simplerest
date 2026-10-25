<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PackFirstDistributionTest extends TestCase
{
    public function testPackDryRunDoesNotBootLegacyApp(): void
    {
        $repo = dirname(__DIR__, 2);
        $dest = sys_get_temp_dir() . '/simplerest-pack-dry-run-' . bin2hex(random_bytes(8));
        $output = $this->execute([
            PHP_BINARY,
            $repo . '/com',
            'pack',
            'build',
            '--dry-run',
            '--profile=core',
            '--dest=' . $dest,
        ], $repo);
        self::assertSame(0, $output['exit'], $output['stderr']);
        self::assertStringContainsString('profile=core', $output['stdout']);
        self::assertStringContainsString('src/framework/FrontController.php', $output['stdout']);
        self::assertDirectoryDoesNotExist($dest);
    }

    public function testPackGeneratesOnlyCuratedMicrocoreFiles(): void
    {
        $repo = dirname(__DIR__, 2);
        $dest = sys_get_temp_dir() . '/simplerest-pack-test-' . bin2hex(random_bytes(8));
        try {
            $output = $this->execute([PHP_BINARY, $repo . '/com', 'pack', 'build', '--profile=core', '--dest=' . $dest], $repo);
            self::assertSame(0, $output['exit'], $output['stderr']);
            self::assertFileExists($dest . '/composer.json');
            self::assertFileExists($dest . '/src/framework/FrontController.php');
            self::assertFileDoesNotExist($dest . '/app.php');
            self::assertFileDoesNotExist($dest . '/archive');
            self::assertFileDoesNotExist($dest . '/docs');
            $manifest = json_decode((string) file_get_contents($dest . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);
            self::assertSame(['php', 'doctrine/inflector'], array_keys($manifest['require']));
        } finally {
            if (is_dir($dest)) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dest, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($iterator as $entry) {
                    $entry->isDir() ? rmdir($entry->getPathname()) : unlink($entry->getPathname());
                }
                rmdir($dest);
            }
        }
    }

    public function testFilesLibraryLoadsFromCanonicalFramework(): void
    {
        $class = 'Boctulus\\Simplerest\\Core\\Libs\\Files';
        self::assertTrue(class_exists($class));
        self::assertStringContainsString(
            'src/framework/Libs/Files.php',
            str_replace('\\', '/', (new ReflectionClass($class))->getFileName())
        );
    }

    private function execute(array $command, string $cwd): array
    {
        $proc = proc_open($command, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $cwd);
        self::assertIsResource($proc);
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        return ['stdout' => $out, 'stderr' => $err, 'exit' => proc_close($proc)];
    }
}
