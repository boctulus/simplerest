<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * The microcore is the whole src/framework tree. "Micro" means it requires
 * nothing but PHP: any library that needs an external dependency lives in its
 * own package under packages/boctulus, which declares that dependency.
 */
final class MicrocoreIsolationTest extends TestCase
{
    /** Composer runtime classes, used only behind class_exists() checks. */
    private const ALLOWED_PREFIXES = [
        'Boctulus\\Simplerest\\',
        'Composer\\',
    ];

    private static function root(): string
    {
        return dirname(__DIR__, 2);
    }

    public function testCoreManifestRequiresOnlyPhp(): void
    {
        $manifest = json_decode((string) file_get_contents(self::root() . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(['php'], array_keys($manifest['require'] ?? []));
        self::assertArrayNotHasKey('require-dev', $manifest);
        self::assertArrayNotHasKey('suggest', $manifest);
        self::assertArrayNotHasKey('autoload-dev', $manifest);
        self::assertArrayNotHasKey('files', $manifest['autoload'] ?? []);
        self::assertSame(['Boctulus\\Simplerest\\Core\\' => 'src/framework/'], $manifest['autoload']['psr-4'] ?? null);
    }

    public function testCoreHasNoExternalLibraryReferences(): void
    {
        $core = self::root() . '/src/framework/';
        $found = [];
        foreach ($this->corePhpFiles($core) as $relative => $source) {
            foreach (token_get_all($source) as $token) {
                if (!is_array($token) || !in_array($token[0], [T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED], true)) {
                    continue;
                }
                $name = ltrim($token[1], '\\');
                if (!str_contains($name, '\\') || $this->isAllowed($name, $relative)) {
                    continue;
                }
                $found[] = "$relative: $name";
            }
        }

        self::assertSame([], array_values(array_unique($found)), 'Move libraries with external dependencies to a package.');
    }

    public function testEveryCoreClassLoadsWithoutVendorOrPackages(): void
    {
        $process = proc_open(
            [PHP_BINARY, __DIR__ . '/fixtures/load-core-classes.php', self::root()],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes
        );
        self::assertIsResource($process);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        self::assertSame(0, proc_close($process), $stderr . $stdout);
        self::assertMatchesRegularExpression('/^loaded=\d+$/m', (string) $stdout);
    }

    public function testExtractedLibrariesLiveInTheirPackages(): void
    {
        $root = self::root();
        foreach ([
            'Api/AuthController.php' => 'simplerest-jwt-auth/src/AuthController.php',
            'Libs/Mail.php' => 'simplerest-mail/src/Mail.php',
            'Libs/EmailTemplate.php' => 'simplerest-mail/src/EmailTemplate.php',
            'Libs/MailBase.php' => 'simplerest-mail/src/MailBase.php',
            'Libs/MailAuditor.php' => 'simplerest-mail/src/MailAuditor.php',
            'Libs/MailFromRemoteWP.php' => 'simplerest-mail/src/MailFromRemoteWP.php',
            'Interfaces/IMail.php' => 'simplerest-mail/src/Interfaces/IMail.php',
            'Psr7/ServerRequestAdapter.php' => 'simplerest-psr7/src/ServerRequestAdapter.php',
            'Helpers/psr7.php' => 'simplerest-psr7/src/helpers.php',
            'Libs/CSS.php' => 'simplerest-css/src/CSS.php',
        ] as $coreFile => $packageFile) {
            self::assertFileDoesNotExist("$root/src/framework/$coreFile");
            self::assertFileExists("$root/packages/boctulus/$packageFile");
        }
    }

    public function testCoreHasNoClassAliases(): void
    {
        foreach ($this->corePhpFiles(self::root() . '/src/framework/') as $relative => $source) {
            self::assertDoesNotMatchRegularExpression('/\bclass_alias\s*\(/i', $source, $relative);
        }
    }

    /** @return array<string, string> relative path => source, Templates excluded */
    private function corePhpFiles(string $core): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($core, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($core)));
            if ($file->getExtension() === 'php' && !str_starts_with($relative, 'Templates/')) {
                $files[$relative] = (string) file_get_contents($file->getPathname());
            }
        }
        ksort($files);
        return $files;
    }

    private function isAllowed(string $name, string $relative): bool
    {
        foreach (self::ALLOWED_PREFIXES as $prefix) {
            if (str_starts_with($name, $prefix)) {
                return true;
            }
        }
        return false;
    }
}
