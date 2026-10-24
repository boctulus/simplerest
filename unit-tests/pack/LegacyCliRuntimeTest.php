<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class LegacyCliRuntimeTest extends TestCase
{
    public function testApplicationAutoloadPointsToActualExtractedHelpers(): void
    {
        $root = dirname(__DIR__, 2);
        require_once $root . '/config/constants.php';

        $autoload = require $root . '/config/autoload.php';
        self::assertSame([], $autoload['exclude'] ?? null);

        foreach ($autoload['include'] as $directory) {
            self::assertDirectoryExists($directory, "Legacy bootstrap helper directory missing: $directory");
        }
        self::assertStringContainsString(
            'src/framework/Helpers',
            str_replace('\\', '/', $autoload['include'][0])
        );
    }

    public function testLegacyCliEntrypointIsKeptOutsideComposerPack(): void
    {
        $root = dirname(__DIR__, 2);
        $entrypoint = (string) file_get_contents($root . '/com');

        self::assertStringContainsString("require_once __DIR__ . '/app.php';", $entrypoint);
        self::assertFileExists($root . '/src/framework/Libs/Files.php');

        // The actual legacy CLI is exercised separately by the
        // legacy-cli-smoke CI job using an ephemeral .env fixture.
        // Never invoke the application bootstrap from a unit test because
        // it could read an operator's real .env or production configuration.
    }

}
