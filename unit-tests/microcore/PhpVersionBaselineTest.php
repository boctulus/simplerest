<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * The 4.x line targets PHP 8.3+. This contract keeps the supported runtime
 * declared in the manifests, the CI matrix, the static analysis config and the
 * README from drifting back to the unmaintained 8.1/8.2 line.
 */
final class PhpVersionBaselineTest extends TestCase
{
    private static function root(): string
    {
        return dirname(__DIR__, 2);
    }

    private static function json(string $file): array
    {
        return json_decode((string) file_get_contents(self::root() . '/' . $file), true, 512, JSON_THROW_ON_ERROR);
    }

    public function testPublicManifestRequiresPhp83(): void
    {
        $manifest = self::json('composer.json');

        self::assertSame('^8.3', $manifest['require']['php'] ?? null);
        self::assertSame('4.0.0', $manifest['version'] ?? null);
        self::assertSame('4.0.x-dev', $manifest['extra']['branch-alias']['dev-master'] ?? null);
    }

    public function testDevelopmentManifestRequiresPhp83AndShipsTheAnalysisTools(): void
    {
        $manifest = self::json('composer-dev.json');

        self::assertSame('^8.3', $manifest['require']['php'] ?? null);
        self::assertArrayHasKey('phpstan/phpstan', $manifest['require-dev'] ?? []);
        self::assertArrayHasKey('rector/rector', $manifest['require-dev'] ?? []);
        self::assertSame(
            'phpstan analyse -c phpstan-core.neon --no-progress',
            $manifest['scripts']['analyse'] ?? null
        );
    }

    public function testCiOnlyRunsSupportedPhpVersions(): void
    {
        $workflow = (string) file_get_contents(self::root() . '/.github/workflows/tests.yml');

        self::assertMatchesRegularExpression(
            "/php-version:\\s*\\['8\\.3',\\s*'8\\.4'\\]/",
            $workflow,
            'The CI matrix must cover exactly PHP 8.3 and 8.4.'
        );
        self::assertDoesNotMatchRegularExpression(
            "/php-version:\\s*\\[[^\\]]*'8\\.[12]'/",
            $workflow,
            'PHP 8.1/8.2 are legacy and must not run in CI.'
        );
    }

    public function testReadmeNoLongerAdvertisesLegacyPhp(): void
    {
        $readme = (string) file_get_contents(self::root() . '/README.md');

        self::assertStringContainsString('PHP `^8.3`', $readme);
        self::assertStringNotContainsString('>=8.1,<8.5', $readme);
    }

    public function testStaticAnalysisConfigurationCoversTheWholeCore(): void
    {
        $config = (string) file_get_contents(self::root() . '/phpstan-core.neon');

        self::assertStringContainsString('src/framework', $config);
        self::assertStringContainsString('phpstan-core-baseline.neon', $config);
        self::assertFileExists(self::root() . '/phpstan-core-baseline.neon');
    }

    public function testCoreSourceDoesNotDeclareLegacyPhpCompatibility(): void
    {
        $manifest = self::json('composer.json');
        $composerSource = (string) file_get_contents(self::root() . '/composer.json');

        self::assertArrayNotHasKey('conflict', $manifest);
        self::assertStringNotContainsString('8.1', $composerSource);
        self::assertStringNotContainsString('8.2', $composerSource);
    }
}
