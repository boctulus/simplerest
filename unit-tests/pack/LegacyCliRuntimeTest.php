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
            'archive/simplerest-framework-extractions-2026-10-23/Helpers',
            str_replace('\\', '/', $autoload['include'][0])
        );
    }

    public function testLegacyCliCanPrintHelpWithoutMissingClassFatal(): void
    {
        $root = dirname(__DIR__, 2);
        $proc = proc_open(
            [PHP_BINARY, '-d', 'display_errors=1', $root . '/com'],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $root
        );
        self::assertIsResource($proc);
        $output = stream_get_contents($pipes[1]);
        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $status = proc_close($proc);

        self::assertSame(0, $status, "Legacy CLI failed:\n" . $output . "\n" . $errors);
        self::assertStringNotContainsString('Fatal error', $output . $errors);
    }
}
