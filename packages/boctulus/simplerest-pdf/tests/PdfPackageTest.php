<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\Libs\PdfBase;
use PHPUnit\Framework\TestCase;

final class PdfPackageTest extends TestCase
{
    private static string $temporaryDirectory = '';

    public static function setUpBeforeClass(): void
    {
        self::$temporaryDirectory = ETC_PATH;
    }

    public static function tearDownAfterClass(): void
    {
        $expectedPrefix = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'simplerest-pdf-package-tests-';
        if (!str_starts_with(self::$temporaryDirectory, $expectedPrefix) || !is_dir(self::$temporaryDirectory)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(self::$temporaryDirectory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $entry) {
            if ($entry->isDir()) {
                rmdir($entry->getPathname());
            } else {
                unlink($entry->getPathname());
            }
        }

        rmdir(self::$temporaryDirectory);
    }

    public function testGeneratesPdfContentUsingInstalledFpdfDependency(): void
    {
        $pdf = new class extends PdfBase {
            public function render(): void {}
        };

        $pdf->AddPage();
        $pdf->Cell(0, 10, 'Composer package smoke test');
        $contents = $pdf->Output('S');

        self::assertStringStartsWith('%PDF-', $contents);
        self::assertDirectoryExists(ETC_PATH . DIRECTORY_SEPARATOR . 'pdfs');
    }
}
