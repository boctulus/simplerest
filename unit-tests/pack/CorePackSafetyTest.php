<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CorePackSafetyTest extends TestCase
{
    private array $temporaryDirectories = [];

    protected function tearDown(): void
    {
        foreach (array_reverse($this->temporaryDirectories) as $directory) {
            $this->removeDirectory($directory);
        }
        $this->temporaryDirectories = [];
        parent::tearDown();
    }

    public function testFreshBuildContainsOnlyCuratedCoreFiles(): void
    {
        $source = $this->createSource();
        $destination = $this->sandboxPath('fresh-destination');

        $result = $this->pack($source, $destination);

        self::assertSame(0, $result['exit'], $result['stderr']);
        self::assertSame(
            ['LICENSE', 'README.md', 'composer.json', 'src/framework/Core.php'],
            array_keys($this->snapshot($destination))
        );
        $manifest = json_decode((string) file_get_contents($destination . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('boctulus/simplerest', $manifest['name']);
        self::assertSame(['php', 'doctrine/inflector'], array_keys($manifest['require']));
        self::assertArrayNotHasKey('scripts', $manifest);
        self::assertFileDoesNotExist($destination . '/scripts/pack/build-core.php');
        self::assertFileDoesNotExist($destination . '/app/Commands/pack/BuildCommand.php');
        self::assertFileDoesNotExist($destination . '/com');
    }

    public function testCleanGitManagedDestinationCanBeRefreshedWithoutReplacingGitMetadata(): void
    {
        $source = $this->createSource();
        $destination = $this->createManagedDestination($source);
        $gitMarker = realpath($destination . '/.git');
        $destinationCommit = $this->git($destination, ['rev-parse', 'HEAD'])['stdout'];

        $result = $this->pack($source, $destination);

        self::assertSame(0, $result['exit'], $result['stderr']);
        self::assertSame($gitMarker, realpath($destination . '/.git'));
        self::assertSame($destinationCommit, $this->git($destination, ['rev-parse', 'HEAD'])['stdout']);
        self::assertSame('', $this->git($destination, ['status', '--porcelain=v1', '--untracked-files=all'])['stdout']);
    }

    public function testDirtyGitDestinationStatesAreRejected(): void
    {
        $source = $this->createSource();

        $unstaged = $this->createManagedDestination($source, 'unstaged');
        file_put_contents($unstaged . '/README.md', "changed\n");
        $this->assertPackRejected($source, $unstaged, 'Git worktree has staged, unstaged, untracked, or ignored files');

        $staged = $this->createManagedDestination($source, 'staged');
        file_put_contents($staged . '/README.md', "changed\n");
        $this->git($staged, ['add', 'README.md']);
        $this->assertPackRejected($source, $staged, 'Git worktree has staged, unstaged, untracked, or ignored files');

        $untracked = $this->createManagedDestination($source, 'untracked');
        file_put_contents($untracked . '/operator-notes.txt', "preserve\n");
        $this->assertPackRejected($source, $untracked, 'Git worktree has staged, unstaged, untracked, or ignored files');
    }

    public function testIgnoredDestinationFileIsRejected(): void
    {
        $source = $this->createSource();
        $destination = $this->createManagedDestination($source);
        file_put_contents($destination . '/.git/info/exclude', "*.secret\n", FILE_APPEND);
        file_put_contents($destination . '/operator.secret', "preserve\n");

        $this->assertPackRejected($source, $destination, 'Git worktree has staged, unstaged, untracked, or ignored files');
        self::assertSame("preserve\n", file_get_contents($destination . '/operator.secret'));
    }

    public function testLegacyDestinationLayoutRequiresExplicitMigration(): void
    {
        $source = $this->createSource();
        $destination = $this->sandboxPath('legacy-destination');
        mkdir($destination, 0777, true);
        file_put_contents($destination . '/composer.json', json_encode(['name' => 'boctulus/simplerest'], JSON_THROW_ON_ERROR));
        file_put_contents($destination . '/app.php', "<?php\n");
        $this->initializeGit($destination);

        $this->assertPackRejected($source, $destination, 'tracked legacy or unrelated content: app.php');
        self::assertFileExists($destination . '/app.php');
    }

    public function testSourceAndDestinationOverlapIsRejected(): void
    {
        $source = $this->createSource();
        $result = $this->pack($source, $source . '/src', ['--dry-run']);

        self::assertSame(1, $result['exit']);
        self::assertStringContainsString('non-overlapping directories', $result['stderr']);
    }

    public function testDefaultBuildRejectsOnlyRelevantDirtySourcePaths(): void
    {
        $source = $this->createSource();
        mkdir($source . '/docs');
        file_put_contents($source . '/docs/operator-note.md', "unrelated\n");
        $unrelated = $this->pack($source, $this->sandboxPath('unrelated-destination'), ['--dry-run']);
        self::assertSame(0, $unrelated['exit'], $unrelated['stderr']);

        file_put_contents($source . '/src/framework/Core.php', "<?php\nfinal class Core { public const VERSION = 2; }\n");
        $tracked = $this->pack($source, $this->sandboxPath('tracked-destination'), ['--dry-run']);
        self::assertSame(1, $tracked['exit']);
        self::assertStringContainsString('Relevant source changes would be omitted', $tracked['stderr']);

        $untrackedSource = $this->createSource('untracked-source');
        file_put_contents($untrackedSource . '/src/framework/NewCore.php', "<?php\nfinal class NewCore {}\n");
        $untracked = $this->pack($untrackedSource, $this->sandboxPath('untracked-source-destination'), ['--dry-run']);
        self::assertSame(1, $untracked['exit']);

        $ignoredSource = $this->createSource('ignored-source');
        file_put_contents($ignoredSource . '/.git/info/exclude', "src/framework/IgnoredCore.php\n", FILE_APPEND);
        file_put_contents($ignoredSource . '/src/framework/IgnoredCore.php', "<?php\nfinal class IgnoredCore {}\n");
        $ignored = $this->pack($ignoredSource, $this->sandboxPath('ignored-source-destination'), ['--dry-run']);
        self::assertSame(1, $ignored['exit']);
    }

    public function testExplicitHistoricalCommitIsTraceableAndIgnoresWorkingTreeState(): void
    {
        $source = $this->createSource();
        $historicalCommit = trim($this->git($source, ['rev-parse', 'HEAD'])['stdout']);
        file_put_contents($source . '/src/framework/Core.php', "<?php\nfinal class Core { public const VERSION = 2; }\n");
        $this->git($source, ['add', 'src/framework/Core.php']);
        $this->git($source, ['commit', '-m', 'test: second source revision']);
        file_put_contents($source . '/src/framework/Core.php', "<?php\nthis working tree is deliberately invalid\n");
        $destination = $this->sandboxPath('historical-destination');

        $result = $this->pack($source, $destination, ['--source-commit=' . $historicalCommit]);

        self::assertSame(0, $result['exit'], $result['stderr']);
        self::assertStringContainsString('source_mode=historical-commit', $result['stdout']);
        self::assertStringContainsString($historicalCommit, $result['stdout']);
        self::assertStringContainsString('VERSION = 1', (string) file_get_contents($destination . '/src/framework/Core.php'));
    }

    public function testStagingFailureLeavesManagedDestinationUnchanged(): void
    {
        $source = $this->createSource();
        $destination = $this->createManagedDestination($source);
        $before = $this->snapshot($destination);
        file_put_contents($source . '/src/framework/Core.php', "<?php\nfinal class Broken {\n");
        $this->git($source, ['add', 'src/framework/Core.php']);
        $this->git($source, ['commit', '-m', 'test: invalid source syntax']);

        $result = $this->pack($source, $destination, ['--source-commit=HEAD']);

        self::assertSame(1, $result['exit']);
        self::assertStringContainsString('PHP syntax validation failed', $result['stderr']);
        self::assertSame($before, $this->snapshot($destination));
        self::assertSame('', $this->git($destination, ['status', '--porcelain=v1', '--untracked-files=all'])['stdout']);
    }

    public function testDryRunPerformsPreflightWithoutMutation(): void
    {
        $source = $this->createSource();
        $destination = $this->createManagedDestination($source);
        $before = $this->snapshot($destination);
        $gitHead = $this->git($destination, ['rev-parse', 'HEAD'])['stdout'];

        $result = $this->pack($source, $destination, ['--dry-run']);

        self::assertSame(0, $result['exit'], $result['stderr']);
        self::assertStringContainsString('Dry-run complete; no files were modified.', $result['stdout']);
        self::assertSame($before, $this->snapshot($destination));
        self::assertSame($gitHead, $this->git($destination, ['rev-parse', 'HEAD'])['stdout']);
    }

    public function testHelpAndErrorsUseCorrectExitCodesWithoutBuilding(): void
    {
        $repository = dirname(__DIR__, 2);
        $help = $this->execute([PHP_BINARY, $repository . '/com', 'pack', 'build', '--help'], $repository);
        self::assertSame(0, $help['exit'], $help['stderr']);
        self::assertStringContainsString('Usage:', $help['stdout']);

        $error = $this->execute([PHP_BINARY, $repository . '/com', 'pack', 'build', '--force'], $repository);
        self::assertSame(1, $error['exit']);
        self::assertStringContainsString('Unsupported pack option: --force', $error['stderr']);
    }

    private function assertPackRejected(string $source, string $destination, string $message): void
    {
        $before = $this->snapshot($destination);
        $result = $this->pack($source, $destination, ['--dry-run']);
        self::assertSame(1, $result['exit']);
        self::assertStringContainsString($message, $result['stderr']);
        self::assertSame($before, $this->snapshot($destination));
    }

    private function createSource(string $name = 'source'): string
    {
        $source = $this->sandboxPath($name);
        mkdir($source . '/src/framework', 0777, true);
        file_put_contents($source . '/LICENSE', "MIT\n");
        file_put_contents($source . '/README.md', "# Test package\n");
        file_put_contents($source . '/src/framework/Core.php', "<?php\nfinal class Core { public const VERSION = 1; }\n");
        file_put_contents($source . '/composer.json', json_encode([
            'name' => 'boctulus/simplerest',
            'require' => [
                'php' => '>=8.1,<8.5',
                'doctrine/inflector' => '^2.0',
            ],
            'autoload' => [
                'psr-4' => ['Boctulus\\Simplerest\\Core\\' => 'src/framework/'],
            ],
            'scripts' => [
                'post-root-package-install' => '@php scripts/composer/create-project.php',
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n");
        $this->initializeGit($source);
        return $source;
    }

    private function createManagedDestination(string $source, string $name = 'managed-destination'): string
    {
        $destination = $this->sandboxPath($name);
        $result = $this->pack($source, $destination);
        self::assertSame(0, $result['exit'], $result['stderr']);
        $this->initializeGit($destination);
        return $destination;
    }

    private function initializeGit(string $directory): void
    {
        $this->git($directory, ['init', '-q']);
        $this->git($directory, ['config', 'user.name', 'PACK Test']);
        $this->git($directory, ['config', 'user.email', 'pack-test@example.invalid']);
        $this->git($directory, ['add', '--all']);
        $this->git($directory, ['commit', '-q', '-m', 'test: initial state']);
    }

    private function pack(string $source, string $destination, array $extra = []): array
    {
        $repository = dirname(__DIR__, 2);
        return $this->execute(array_merge([
            PHP_BINARY,
            $repository . '/com',
            'pack',
            'build',
            '--profile=core',
            '--source=' . $source,
            '--dest=' . $destination,
        ], $extra), $repository);
    }

    private function git(string $directory, array $arguments): array
    {
        $result = $this->execute(array_merge(['git', '-C', $directory], $arguments), $directory);
        self::assertSame(0, $result['exit'], $result['stderr']);
        return $result;
    }

    private function execute(array $command, string $cwd): array
    {
        $process = proc_open($command, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $cwd);
        self::assertIsResource($process);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        return ['stdout' => $stdout, 'stderr' => $stderr, 'exit' => proc_close($process)];
    }

    private function sandboxPath(string $name): string
    {
        if ($this->temporaryDirectories === []) {
            $root = sys_get_temp_dir() . '/simplerest-pack-safety-' . bin2hex(random_bytes(8));
            mkdir($root, 0777, true);
            $this->temporaryDirectories[] = $root;
        }
        return $this->temporaryDirectories[0] . '/' . $name . '-' . bin2hex(random_bytes(4));
    }

    private function snapshot(string $directory): array
    {
        if (!is_dir($directory)) {
            return [];
        }
        $snapshot = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $entry) {
            $path = str_replace('\\', '/', $entry->getPathname());
            if ($entry->isFile() && !str_contains($path, '/.git/')) {
                $relative = substr($path, strlen(str_replace('\\', '/', $directory)) + 1);
                $snapshot[$relative] = hash_file('sha256', $entry->getPathname());
            }
        }
        ksort($snapshot);
        return $snapshot;
    }

    private function removeDirectory(string $directory): void
    {
        $real = realpath($directory);
        $temp = realpath(sys_get_temp_dir());
        if ($real === false || $temp === false || dirname($real) !== $temp
            || !str_starts_with(basename($real), 'simplerest-pack-safety-')) {
            return;
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($real, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $entry) {
            if ($entry->isLink() || $entry->isFile()) {
                @chmod($entry->getPathname(), 0666);
                unlink($entry->getPathname());
            } else {
                @chmod($entry->getPathname(), 0777);
                rmdir($entry->getPathname());
            }
        }
        rmdir($real);
    }
}
